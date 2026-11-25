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

Campos enviados: `last_name` (nome completo), `phone`, `email`, `company`,
`description`, ocultos `oid`, `retURL`, `lead_source=Web`. Há uma armadilha
anti-robô (`website`) que o `theme.js` barra e desativa antes do POST.

## UTMs

`assets/js/utm-web-to-lead.js` é carregado em **todas** as páginas
(`inc/enqueue.php`), porque as UTMs só existem na URL da página de entrada.

1. No carregamento lê `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`,
   `utm_content`, `gclid`, `fbclid` e guarda no `localStorage` (`cli_utms`,
   90 dias, último clique vence).
2. No envio de `form[action*="WebToLead"]`, cria os campos ocultos `00N...`
   configurados e acrescenta à Descrição:

```
--- Origem do lead ---
utm_source: google
utm_medium: cpc
utm_campaign: sap brasil
formulario: whatsapp
pagina: /solucao/integracao-sap/
```

Sem UTM, o bloco diz `Sem UTM (orgânico/direto)`.

## Configuração (Customizer › Salesforce — Web-to-Lead)

- **ID da organização**: já vem com `00Das000004fL2z`.
- **ID do campo — UTM Source…FBCLID**: os IDs `00N...` dos campos
  personalizados do Lead. Vazio = o valor vai só na Descrição.

Para ter as UTMs em campos próprios no Salesforce:

1. Criar no Lead os campos de texto (255) `UTM_Source__c`, `UTM_Medium__c`,
   `UTM_Campaign__c`, `UTM_Term__c`, `UTM_Content__c` (e `GCLID__c`,
   `FBCLID__c`, se quiser).
2. Em Configuração › Web-to-Lead, gerar o formulário marcando esses campos e
   copiar o `name="00N..."` de cada um para o Customizer.
3. Mapear os campos em Lead › Campos › Mapear campos de lead (senão as UTMs se
   perdem na conversão).

## Testes

1. Aba anônima em `/?utm_source=teste&utm_medium=cpc&utm_campaign=teste`;
   no console, `JSON.parse(localStorage.getItem('cli_utms'))` mostra as UTMs.
2. Navegar pelo menu, abrir o botão de WhatsApp e enviar. No DevTools ›
   Network (filtro `WebToLead`) o payload traz a Descrição com o bloco de
   origem (e os `00N...`, se configurados). Depois do envio, cai no WhatsApp.
3. Enviar pela página Contato: volta para `/contato/?enviado=1` com a mensagem.
4. Depurar do lado do Salesforce: adicionar temporariamente ao formulário
   `debug=1` e `debugEmail` (ver `webtolead-salesforce.html` original).

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
