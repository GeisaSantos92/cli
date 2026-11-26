# Leads: Salesforce Web-to-Lead + UTMs

O site tem **um único formulário de lead**, o Web-to-Lead do Salesforce (org
`00Das000004fL2z`). Ele não passa pelo WordPress: é o POST nativo do formulário
para `webto.salesforce.com`. Não há plugin de formulário nem backend.

## Onde o formulário aparece

| Lugar | Template | Depois do envio (`retURL`) |
|---|---|---|
| Página Contato | `template-parts/contato/formulario.php` | A própria página com `?enviado=1#formulario` (mensagem de sucesso) |
| Popup do botão flutuante de WhatsApp | `template-parts/lead-popup.php` (no `footer.php`) | A conversa do WhatsApp (Customizer `cliconnect_whatsapp_url`) |

Os dois usam o mesmo markup: `template-parts/form-lead.php`.

- **Botões de conversão** (CTAs das soluções, home, Programa de Parceiros, CTA
  azul do rodapé) levam para a **página Contato** do idioma atual.
- **Botão flutuante de WhatsApp** e o ícone de WhatsApp da página Contato
  (`data-lead-popup`) abrem o popup. Sem JS, o link segue direto para o
  WhatsApp.
- `?enviado=1` serve de gatilho de conversão no Google Ads / Meta Ads (URL
  contém `enviado=1`) e recebe `noindex`.

Campos enviados: `last_name` (nome completo), `phone`, `email` (e-mail
corporativo), `company`, ocultos `oid`, `retURL`, `lead_source=Web` e os 7
campos de UTM `00N...`. **Não há campo de mensagem** (`description`), a pedido
do cliente. Há uma armadilha anti-robô (`website`) que o `theme.js` barra e
desativa antes do POST — ela não chega ao Salesforce.

## UTMs (v3, 26/11/2026)

`assets/js/utm-web-to-lead.js` é carregado em **todas** as páginas
(`inc/enqueue.php`), porque as UTMs só existem na URL da página de entrada.

1. Na chegada a qualquer página, lê `utm_source`, `utm_medium`,
   `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `fbclid` e guarda no
   `localStorage` (`cli_utms`, 90 dias, último clique em anúncio vence).
2. Preenche os campos ocultos `00N...` de todo `form[action*="WebToLead"]`:
   no carregamento, quando um formulário aparece depois (popup) e no envio.
   Se algum input faltar no HTML, o script o cria.

As UTMs vão **só para os campos do Lead**; nada é escrito na Descrição.

### Mapeamento dos campos

IDs tirados do Web-to-Lead gerado pelo Salesforce em 25/11/2026. Diferenciam
maiúsculas de minúsculas (`...2Ka` ≠ `...2KZ`).

| Parâmetro na URL | Campo no Lead | ID no Web-to-Lead |
|---|---|---|
| `utm_source` | UTM Source | `00NbJ00000db2Kc` |
| `utm_medium` | UTM Medium | `00NbJ00000db2Kb` |
| `utm_campaign` | UTM Campaign | `00NbJ00000db2KZ` |
| `utm_term` | UTM Term | `00NbJ00000db2Kd` |
| `utm_content` | UTM Content | `00NbJ00000db2Ka` |
| `gclid` | GCLID | `00NbJ00000db2KY` |
| `fbclid` | FBCLID | `00NbJ00000db2KX` |

Os IDs estão em dois lugares, que mudam juntos se o Salesforce recriar um
campo:

- `assets/js/utm-web-to-lead.js` (`SF_FIELDS`, fixos no script);
- `inc/lead.php` (`cliconnect_lead_campos_padrao()`), que gera os inputs
  ocultos do `form-lead.php` e o `window.cliconnectLead` do enqueue.

## Configuração (Customizer › Salesforce — Web-to-Lead)

- **ID da organização**: já vem com `00Das000004fL2z`.
- **ID do campo — UTM Source…FBCLID**: já vêm com os IDs da tabela acima.
  Vazio = vale o ID padrão. O script só troca um ID quando o valor vem
  preenchido, então um ID vazio nunca apaga o correto.

No Salesforce, os campos precisam estar mapeados em Lead › Campos › Mapear
campos de lead, senão as UTMs se perdem na conversão.

## Testes

1. Aba anônima em
   `/?utm_source=teste&utm_medium=cpc&utm_campaign=teste-v3&utm_content=criativo-1&utm_term=termo-1`.
2. No console, `CLIUtm.utms` mostra as 5 UTMs e `CLIUtm.fields` os 7 IDs
   `00NbJ...`, nenhum vazio.
3. Navegar pelo menu (URL sem UTM), abrir o botão flutuante e, antes de
   enviar, inspecionar o formulário: os inputs `00NbJ...` já têm os valores.
4. Enviar. No DevTools › Network (filtro `WebToLead`) o payload traz os 7
   IDs, sem `description`. Depois do envio, cai no WhatsApp.
5. No Salesforce, o lead tem UTM Source, Medium, Campaign, Term e Content
   preenchidos.
6. Teste orgânico: aba anônima nova em `/` sem UTM; o lead entra com os
   campos de UTM vazios.
7. Enviar pela página Contato: volta para `/contato/?enviado=1` com a mensagem.

Depurar do lado do Salesforce: adicionar temporariamente ao formulário os
ocultos `debug=1` e `debugEmail`; o Salesforce manda por e-mail os campos
recebidos. Remover antes de publicar e apagar os leads de teste.

Depois do deploy, limpar o cache do site (plugin de cache e CDN) para
ninguém continuar recebendo o script antigo.

Limite do Web-to-Lead: 500 leads/dia por org (o excedente fica em fila).

## Migração de um banco antigo

Sites semeados antes desta mudança têm os CTAs gravados com `wa.me`. A
correção (`inc/lead-migracao.php`) **roda sozinha uma vez**, na primeira
requisição depois do deploy, e marca a opção `cliconnect_migracao_whatsapp`.
Não é preciso rodar nada no servidor. Para simular ou repetir à mão:

```bash
./bin/wp cliconnect migrar-whatsapp --dry-run   # lista o que muda
./bin/wp cliconnect migrar-whatsapp
```

Ela troca os campos ACF com link de WhatsApp pelo Contato do idioma do post,
esvazia a URL do CTA do rodapé, troca o texto "Fale conosco no WhatsApp" por
"Fale conosco" e grava a tradução dele no Polylang. Mantém o botão flutuante
e o ícone da página Contato.
