# Analisar a issue

Como transformar uma issue curta num diagnóstico honesto. As issues deste projeto são
enxutas, em português, em tópicos — e a especificação real costuma estar **na imagem
anexada**.

---

## 1. Coletar tudo

```bash
.claude/skills/implementar-issue/scripts/issue.sh <numero> --repo <owner/nome> --destino /tmp/issue-<numero>
```

O script devolve: título, estado, labels, responsáveis, milestone, descrição, **todos os
comentários** e os anexos baixados.

Por que não usar `gh issue view` puro: os anexos ficam em
`https://github.com/user-attachments/assets/...` e, em **repositório privado**, essa URL
devolve **404 sem autenticação**. O script baixa com o token do `gh`
(`curl -H "Authorization: Bearer $(gh auth token)"`) e resolve a extensão pelo
`Content-Type`.

Depois, **leia cada imagem com `Read`**. Um comentário posterior pode alterar ou
cancelar o pedido original — leia a issue na ordem cronológica e trate o comentário mais
recente como a versão vigente.

Issues relacionadas (`#12`, "como na issue anterior") → abra também, com o mesmo script.

---

## 2. Ler a imagem como especificação

A imagem costuma trazer o que o texto não diz. Extraia dela:

| Procure | Exemplo real (#13) |
|---|---|
| **Estados lado a lado** | linha de cima = repouso, linha de baixo = hover |
| **Elementos que aparecem/somem** | a seta ↗ só existe no estado de hover |
| **Variações de cor** | fundo claro vs. azul sólido = duas variantes do mesmo cartão |
| **Setas, círculos, anotações** | marcação do autor apontando o alvo |
| **Antes/depois** | "está assim / deveria ficar assim" |
| **Recorte** | o que está fora do enquadramento provavelmente está fora do escopo |

Case cada detalhe visual com o design system (`assets/css/theme.css`): a cor do hover
quase sempre é um token existente, e o ícone quase sempre já está em
`cliconnect_icone()`. Ver `.claude/skills/criar-pagina/references/padroes-tema.md`.

Se a imagem for ambígua (não dá para dizer se é hover ou estado ativo), **isso é uma
dúvida do briefing**, não um palpite.

---

## 3. Classificar a issue

| Tipo | Sinais | Onde costuma bater |
|---|---|---|
| **Ajuste visual** | "conforme o Figma", "corrigir hover/espaçamento/tamanho" | `assets/css/*.css`, template-part |
| **Conteúdo** | "trocar o texto", "atualizar os números" | `inc/cli/seed.php` (e o campo ACF, se faltar) |
| **Estrutura de seção** | "substituir elemento", "adicionar bloco" | `template-parts/`, `inc/acf-fields-*.php`, CSS |
| **Asset** | "usar a imagem em assets/img/..." | `assets/img/` + `cliconnect_imagem_tema()` |
| **Comportamento** | "não abre", "não fecha", "quebra no mobile" | `assets/js/theme.js`, CSS responsivo |
| **Dado do cliente** | telefone, redes, logo | `inc/customizer.php` — **não** ACF |
| **Página nova** | "criar a página X" | pare: use a skill `criar-pagina` |

Um detalhe importante do projeto: **texto exibido não se corrige no template.** Se a
issue pede outro título de seção, o que muda é o valor no ACF — via `inc/cli/seed.php`
(para o ambiente nascer certo) ou pelo painel. Alterar a string no PHP violaria a regra
de "nada de texto fixo".

---

## 4. Investigar antes de propor

1. **Localize** — `Grep` pela classe CSS da imagem, pelo nome do campo, pelo texto
   exibido, pelo nome da seção.
2. **Leia** o arquivo inteiro em volta do trecho: pode haver comentário explicando a
   decisão atual (o tema documenta bastante no próprio código).
3. **Confirme o sintoma** — para issue visual, capture a página:
   ```bash
   node .claude/skills/criar-pagina/scripts/captura.mjs "http://<site>.local/" /tmp/issue-<n>/antes
   ```
   e compare com o anexo. Isso também vira o "antes" da validação da Fase 6.
4. **Ache a causa.** "O hover não aparece" pode ser CSS ausente, especificidade perdida,
   markup sem o elemento, ou JS que não marca a classe. Corrigir o sintaxe errado gera
   retrabalho.

---

## 5. Sinais de alerta para levar ao briefing

- **Pedido conflita com `CLAUDE.md`** (plugin novo, bloco FSE, CDN de fonte, CSS inline)
  → aponte e proponha a alternativa dentro do padrão.
- **Issue vaga** ("melhorar o rodapé") → liste as interpretações possíveis e peça a
  escolha; não escolha por conta.
- **Imagem sem contexto** — não dá para saber qual página/seção → pergunte.
- **Escopo inflado** — a issue pede uma coisa e a imagem mostra cinco → confirme o
  recorte antes.
- **Já resolvido** — o código já faz o que a issue pede. Diga isso, mostre a evidência e
  pergunte se fecha a issue em vez de "implementar" algo que já existe.
- **Regressão provável** — o ajuste afeta outra tela (o mesmo `.nav-cartao` aparece no
  mega menu) → liste o impacto colateral no briefing.

---

## 6. Estrutura do briefing

Sempre estas cinco partes, nesta ordem:

1. **O que a issue pede** — sua leitura do texto **e** da imagem, separadamente.
2. **O que encontrei no código** — arquivo:linha e o estado atual.
3. **O que vou fazer** — lista numerada, arquivo por arquivo, incluindo seed se houver.
4. **Dúvidas / decisões** — cada ambiguidade com a sua proposta de resolução.
5. **Fora de escopo** — o que você deliberadamente não vai tocar.

Curto e concreto. O objetivo é o usuário conseguir dizer "sim" ou "não, é assim" em
poucos segundos.
