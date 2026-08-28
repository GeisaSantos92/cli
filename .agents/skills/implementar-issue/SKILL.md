---
name: implementar-issue
description: >
  Implementa issues do GitHub neste projeto usando a CLI `gh`. Lê a issue por inteiro
  (descrição, comentários e imagens anexadas), investiga o código afetado, apresenta ao
  usuário um diagnóstico do que está errado e um plano do que será alterado — e só
  implementa depois do OK. Fecha com validação, commit/PR e comentário na issue. Use
  quando pedirem "implementar a issue #X", "resolver a issue", "pega as issues abertas",
  "corrigir o que está no GitHub", "trabalhar nas issues".
---

# Implementar issues do GitHub

O portão de confirmação é o coração desta skill: **entender a issue, dizer ao usuário o
que precisa ser corrigido e esperar o OK antes de tocar em qualquer arquivo.**

Trabalho de código segue as regras do projeto — `AGENTS.md` e `docs/code-standards.md`.
Se a issue for sobre criar uma página nova, use a skill `criar-pagina` em vez desta.

---

## Fase 1 — Contexto: repositório e issues

1. **Descobrir o repositório.**
   ```bash
   git rev-parse --is-inside-work-tree 2>/dev/null && gh repo view --json nameWithOwner -q .nameWithOwner
   ```
   - Dentro de um repo git com remote → o `gh` resolve sozinho, sem `--repo`.
   - **Fora de um repo git** (é o caso desta pasta) → todo comando `gh` precisa de
     `--repo owner/nome`. Confirme com o usuário qual é (para este tema, o provável é
     `daniilomello/cli-connect` — **pergunte, não assuma**) e avise: sem git local,
     **não dá para criar branch, commit nem PR**; a entrega será os arquivos alterados.
     Ofereça `git init` + `git remote add` se ele quiser o fluxo completo.

2. **Descobrir quais issues.** Se o usuário não disse o número:
   ```bash
   gh issue list --repo <repo> --state open --limit 20 \
     --json number,title,labels,updatedAt \
     -q '.[] | "#\(.number) \(.title) — \(.labels|map(.name)|join(","))"'
   ```
   Mostre a lista e pergunte quais implementar (uma, várias ou todas).

3. **Várias issues** → trate uma por vez, do começo ao fim (análise → OK → implementação
   → validação), com commit próprio. Só agrupe se o usuário pedir, e mesmo assim
   apresente um briefing por issue.

Detalhes e receitas do `gh`: [`references/contexto-repo.md`](references/contexto-repo.md).

---

## Fase 2 — Ler a issue INTEIRA

```bash
.Codex/skills/implementar-issue/scripts/issue.sh <numero> \
  --repo <owner/nome> --destino /tmp/issue-<numero>
```

O script imprime título, estado, labels, descrição e **todos os comentários**, e baixa
os **anexos de imagem** para a pasta de destino (usa o token do `gh` — em repositório
privado a URL do anexo dá 404 sem autenticação).

**Leia cada imagem com `Read`.** Nas issues deste projeto a especificação real quase
sempre está na imagem, não no texto: o texto diz "conforme a imagem abaixo". Pular esse
passo é a principal causa de implementar a coisa errada.

Guia de leitura e classificação:
[`references/analise-issue.md`](references/analise-issue.md).

---

## Fase 3 — Investigar o código antes de opinar

Nunca proponha correção a partir só do texto da issue. Antes:

1. **Localize** os arquivos envolvidos (`Grep`/`Glob` por classe CSS, nome de campo ACF,
   texto exibido, nome de template-part).
2. **Leia** o estado atual desses arquivos e entenda por que está como está — muitas
   vezes há uma decisão registrada em comentário ou em `docs/architecture.md`.
3. **Reproduza** quando for visual: rode a captura da página afetada
   (`.Codex/skills/criar-pagina/scripts/captura.mjs`) e compare com a imagem da issue.
4. Identifique a **causa**, não só o sintoma.

Se a issue estiver ambígua, incompleta ou contradizer uma regra do projeto, isso vai
para o briefing da Fase 4 — não invente a interpretação mais conveniente.

---

## Fase 4 — Briefing de confirmação (PORTÃO — não pule)

Apresente ao usuário, em texto, **antes de qualquer edição**:

```
## #13 — Corrigir hover do dropdown

**O que a issue pede** (minha leitura)
- Texto: o dropdown do menu não está conforme o Figma; aplicar o hover correto.
- Imagem: cartões do painel em dois estados — repouso (fundo claro / azul sólido, sem
  ícone) e hover (fundo levemente mais escuro, seta ↗ no canto superior direito,
  sombra sutil).

**O que encontrei no código**
- `assets/css/theme.css:496` — `.nav-cartao` tem hover, mas só muda a borda.
- A seta ↗ não existe no markup; `cliconnect_icone('seta-nordeste')` já está disponível.

**O que vou fazer**
1. `inc/menu-walker.php` — incluir a seta em cada cartão do painel.
2. `assets/css/theme.css` — estado de hover/focus: fundo, sombra e revelação da seta.
3. Sem mudança de conteúdo → seed não é afetado.

**Dúvidas / decisões**
- A seta aparece só no hover ou é permanente e só muda de cor? Na imagem, o estado de
  repouso não a mostra — vou implementar como "aparece no hover".

**Fora de escopo**
- O mega menu de Soluções não é citado na issue; não vou mexer.
```

Depois do briefing, chame `AskUserQuestion`:

| Pergunta | Opções |
|---|---|
| Confirma o entendimento e o plano da #X? | **Sim, implementar** · **Ajustar** (o usuário descreve) · **Não implementar agora** |
| Como entregar? | **Branch + commit + PR** · **Branch + commit** (sem PR) · **Só alterar os arquivos** (sem git) |

Regras do portão:

- Se a resposta pedir ajuste, **refaça o briefing** e pergunte de novo.
- A pergunta de entrega só existe se houver git local; sem git, informe a limitação.
- Se a issue estiver vaga demais para um plano honesto, diga isso e peça a informação
  que falta (ou proponha comentar na issue pedindo esclarecimento) em vez de chutar.
- Se o pedido da issue conflitar com `AGENTS.md` (ex.: pede plugin novo, bloco FSE,
  CSS inline), aponte o conflito no briefing, proponha a alternativa dentro do padrão
  e deixe a decisão com o usuário.

---

## Fase 5 — Implementar

Só depois do OK. Regras não negociáveis (resumo de `AGENTS.md`):

- Prefixo `cliconnect_`, text-domain `cli`, guarda `ABSPATH` no topo de todo PHP.
- Coalescência nula em dado de ACF/Customizer; escape em toda saída.
- CSS só com tokens de `theme.css`; nada de estilo inline nem `!important`.
- Sem build, sem plugin novo, sem FSE.
- Conteúdo textual em ACF/CPT — se a issue muda texto exibido, muda-se o **seed**
  (`inc/cli/seed.php`), não o template.
- **Escopo fechado:** só o que foi acordado no briefing. Achou outro problema pelo
  caminho? Anote e reporte na entrega; não corrija de carona.

---

## Fase 6 — Validar

1. **Sintaxe:** `php -l` em todo arquivo PHP alterado (`PHP_BIN` vem de `bin/wp.config.sh`).
2. **Conteúdo:** se mexeu no seed → `./bin/wp cliconnect seed`.
3. **Visual:** recapture a página afetada e **compare com a imagem da issue**:
   ```bash
   node .Codex/skills/criar-pagina/scripts/captura.mjs "http://<site>.local/<rota>/" /tmp/issue-<n>/depois
   ```
   Para estado de hover, descreva no relato como testou (a captura estática não pega
   `:hover` — inspecione o CSS e, se necessário, valide manualmente com o usuário).
4. **Higiene:** sem erro de console, sem 404 de asset, sem notice no `debug.log`.
5. Confira item a item que **cada ponto do briefing** foi atendido.

---

## Fase 7 — Entregar

Conforme a opção escolhida na Fase 4:

**Branch e commit** (nunca commite direto na `main`):
```bash
git checkout -b fix/13-hover-dropdown
git add -A && git commit   # mensagem convencional, ver contexto-repo.md
```

**PR:**
```bash
gh pr create --title "fix: hover dos cartões do dropdown" --body "Closes #13 ..." --fill-verbose
```

**Comentário na issue** (quando não houver PR):
```bash
gh issue comment 13 --repo <repo> --body "..."
```

**Fechar a issue**: só se o usuário pedir — `gh issue close`. Um PR com `Closes #13`
fecha sozinho no merge; não feche à mão nesse caso.

Feche a conversa com: arquivos alterados, como validou, o que ficou fora, e um pedido
explícito para o usuário conferir o resultado.

Modelos de mensagem de commit, corpo de PR e comentário:
[`references/entrega.md`](references/entrega.md).
