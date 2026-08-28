# Abrir issues a partir do relatório

Só depois do aval do usuário (`AskUserQuestion`). O relatório é o entregável; as issues
são opcionais.

Repositório: `GeisaSantos92/cli`. Use a CLI `gh` — a mesma que `implementar-issue` consome
do outro lado, então escreva pensando em quem vai executá-la.

---

## Estrutura: uma issue mãe + sub-issues

Um achado por sub-issue, agrupado por **seção**, não por tipo de defeito — quem for
corrigir abre um arquivo por vez.

```bash
gh issue create --repo GeisaSantos92/cli --title "..." \
  --body-file corpo.md --assignee <login> --label "figma-home,bug"
```

Vincular como sub-issue exige o `id` numérico (não o `number`):

```bash
ID=$(gh api repos/GeisaSantos92/cli/issues/$N --jq .id)
gh api --method POST repos/GeisaSantos92/cli/issues/$MAE/sub_issues -F sub_issue_id="$ID"
```

Crie as sub-issues primeiro, depois a mãe com a checklist, depois vincule.

---

## Corpo de uma sub-issue

Escrito para um agente executar sem ter participado da revisão:

1. **Contexto** — uma ou duas linhas sobre o que está errado.
2. **Frame no Figma** — deep link do node-id da seção:
   `https://www.figma.com/design/<fileKey>/<nome>?node-id=<id-com-hífen>`
   É o que evita o agente ter que redescobrir a árvore do frame.
3. **O que está diferente** — tabela Figma × site, com os números da Fase 3.
4. **Arquivos** — caminhos exatos, com a linha ou o bloco de CSS.
5. **Plano de implementação** — passos numerados, incluindo os efeitos colaterais do
   projeto: Polylang ao mexer em menu, attachment reaproveitado por nome ao trocar asset
   de seed, `--largura-conteudo` compartilhado por todas as páginas.
6. **Critério de aceite** — verificável, e **sem altura**.

## Corpo da issue mãe

**Objetivo**, link do frame, checklist das sub-issues, **mapa das seções** (tabela node-id
↔ template-part, direto da Fase 1) e a lista **"conferido e correto — não mexer"** da
Fase 5. Essa última é o que impede a próxima revisão de reabrir o que já foi descartado.
