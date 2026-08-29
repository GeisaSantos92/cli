---
name: implementar-issue
description: >
  Implementa issues do GitHub neste projeto usando a CLI `gh`. Lê a issue por inteiro
  (descrição, comentários e imagens anexadas), investiga o código afetado, apresenta ao
  usuário um diagnóstico do que está errado e um plano do que será alterado — e só
  implementa depois do OK. Fecha com validação, commit direto na `main`, sincronização
  da main local e comentário na issue. Use
  quando pedirem "implementar a issue #X", "resolver a issue", "pega as issues abertas",
  "corrigir o que está no GitHub", "trabalhar nas issues".
---

# Implementar issues do GitHub

O portão de confirmação é o coração desta skill: **entender a issue, dizer ao usuário o
que precisa ser corrigido e esperar o OK antes de tocar em qualquer arquivo.**

Trabalho de código segue as regras do projeto — `CLAUDE.md` e `docs/code-standards.md`.
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
     **não dá para commitar**; a entrega será os arquivos alterados.
     Ofereça `git init` + `git remote add` se ele quiser o fluxo completo.

2. **Descobrir quais issues.** Se o usuário não disse o número:
   ```bash
   gh issue list --repo <repo> --state open --limit 20 \
     --json number,title,labels,updatedAt \
     -q '.[] | "#\(.number) \(.title) — \(.labels|map(.name)|join(","))"'
   ```
   Mostre a lista e pergunte quais implementar (uma, várias ou todas).

3. **Várias issues** → trate uma por vez, do começo ao fim (análise → OK → implementação
   → validação → commit na `main` → main local atualizada). Só comece a próxima depois
   que a Fase 7 da anterior estiver fechada. Só agrupe se o usuário pedir, e mesmo assim
   apresente um briefing por issue.

Detalhes e receitas do `gh`: [`references/contexto-repo.md`](references/contexto-repo.md).

---

## Fase 2 — Ler a issue INTEIRA

```bash
.claude/skills/implementar-issue/scripts/issue.sh <numero> \
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
   (`.claude/skills/criar-pagina/scripts/captura.mjs`) e compare com a imagem da issue.
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
| Como entregar? | **Commit direto na `main`** (padrão) · **Só alterar os arquivos** (sem commit) |

Regras do portão:

- Se a resposta pedir ajuste, **refaça o briefing** e pergunte de novo.
- A pergunta de entrega só existe se houver git local; sem git, informe a limitação.
  **Nunca ofereça branch nem PR** — este repositório trabalha direto na `main`.
- Se a issue estiver vaga demais para um plano honesto, diga isso e peça a informação
  que falta (ou proponha comentar na issue pedindo esclarecimento) em vez de chutar.
- Se o pedido da issue conflitar com `CLAUDE.md` (ex.: pede plugin novo, bloco FSE,
  CSS inline), aponte o conflito no briefing, proponha a alternativa dentro do padrão
  e deixe a decisão com o usuário.

---

## Fase 5 — Implementar

Só depois do OK. Regras não negociáveis (resumo de `CLAUDE.md`):

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
   node .claude/skills/criar-pagina/scripts/captura.mjs "http://<site>.local/<rota>/" /tmp/issue-<n>/depois
   ```
   Para estado de hover, descreva no relato como testou (a captura estática não pega
   `:hover` — inspecione o CSS e, se necessário, valide manualmente com o usuário).
4. **Higiene:** sem erro de console, sem 404 de asset, sem notice no `debug.log`.
5. Confira item a item que **cada ponto do briefing** foi atendido.

---

## Fase 7 — Entregar

O fluxo deste repositório é **commit direto na `main`**. Não crie branch de feature e
**nunca abra PR** — nem para "facilitar a revisão", nem porque a mudança é grande.

1. **Confira o ponto de partida.**
   ```bash
   git rev-parse --abbrev-ref HEAD   # tem que ser main
   git status --short                # só os arquivos desta issue
   git pull --rebase                 # traz o que já subiu de outra máquina/sessão
   ```
   Se a sessão estiver numa worktree ou em outra branch, traga o trabalho para a `main`
   antes de commitar — receita em [`references/contexto-repo.md`](references/contexto-repo.md) §4.

2. **Commit** — um por issue, Conventional Commits, `Closes #N` no corpo (modelo em
   [`references/entrega.md`](references/entrega.md)):
   ```bash
   git add <caminhos da issue>       # -A só se o working tree tiver apenas isso
   git commit -m "fix(menu): ..."
   ```

3. **Atualizar a main local — obrigatório antes de concluir.** A issue só está entregue
   quando a `main` local contém o commit, está limpa e não está atrás do remoto:
   ```bash
   git pull --rebase
   git push                          # publica — só com OK do usuário
   git status -sb                    # limpo, sem "behind"
   git log --oneline -3
   ```
   Se o usuário não autorizar o `push`, tudo bem — mas diga explicitamente que a `main`
   local está à frente do remoto e o que falta para publicar.

4. **Comentário na issue** (opcional, só com autorização — é público):
   ```bash
   gh issue comment 13 --repo <repo> --body "Resolvido na main em <sha>. ..."
   ```

5. **Fechar a issue**: só se o usuário pedir — `gh issue close`. Sem PR, nada fecha
   sozinho; não feche por conta própria.

**Antes de iniciar a próxima issue**, repita o passo 3: `main` local limpa, com o commit
da issue anterior dentro e sincronizada. Nunca comece uma issue nova em cima de trabalho
não commitado — o commit seguinte misturaria as duas.

Feche a conversa com: arquivos alterados, como validou, o que ficou fora, o sha do commit
e o estado da `main` (local e remoto), e um pedido explícito para o usuário conferir.

Modelos de mensagem de commit e comentário:
[`references/entrega.md`](references/entrega.md).
