# Contexto do repositório e receitas do `gh`

---

## 1. Onde o código está vs. onde a issue está

Os dois nem sempre coincidem. Resolva **antes** de qualquer coisa:

```bash
git rev-parse --is-inside-work-tree 2>/dev/null || echo "SEM GIT"
gh repo view --json nameWithOwner,defaultBranchRef -q '"\(.nameWithOwner) (\(.defaultBranchRef.name))"' 2>/dev/null
gh auth status
```

| Situação | O que fazer |
|---|---|
| Repo git com remote no GitHub | `gh` resolve sozinho; `--repo` é opcional |
| Repo git sem remote | descubra o repo com o usuário e use `--repo` em todo comando `gh` |
| **Pasta sem git** (caso desta cópia do tema) | `--repo` obrigatório; **avise que branch/commit/PR não são possíveis** e ofereça `git init` |
| `gh auth status` falhando | peça ao usuário `gh auth login` (é interativo — sugira `! gh auth login`) |

Nunca rode `git init`, `git remote add` ou `git clone` por conta própria: são mudanças
estruturais no ambiente do usuário. Proponha e espere o OK.

---

## 2. Receitas

**Listar e filtrar**
```bash
gh issue list --repo <r> --state open --limit 20 \
  --json number,title,labels,updatedAt \
  -q '.[] | "#\(.number) \(.title) — \(.labels|map(.name)|join(","))"'

gh issue list --repo <r> --label bug --state open
gh issue list --repo <r> --search "hover in:title"
```

**Ler** — prefira o script (traz comentários + baixa anexos):
```bash
.claude/skills/implementar-issue/scripts/issue.sh <n> --repo <r> --destino /tmp/issue-<n>
```

**Escrever na issue**
```bash
gh issue comment <n> --repo <r> --body "..."
gh issue comment <n> --repo <r> --body-file /tmp/comentario.md   # texto longo
gh issue edit <n> --repo <r> --add-label bug
gh issue close <n> --repo <r> --comment "Resolvido em <sha>"      # só com OK do usuário
```

**Contexto extra**
```bash
gh api repos/<r>/commits -q '.[0:5][] | "\(.sha[0:7]) \(.commit.message|split("\n")[0])"'
gh pr list --repo <r> --state all --limit 5
gh issue view <n> --repo <r> --web    # abre no navegador do usuário
```

Ações de escrita (`comment`, `close`, `edit`, `pr create`) são **visíveis para outras
pessoas**. Só execute com autorização explícita para aquela ação.

---

## 3. Branch e commit

Convenção observada no repositório (`daniilomello/cli-connect`): **Conventional
Commits** (`feat:`, `fix:`, `docs:`, `refactor:`, `style:`, `chore:`), em uma linha,
em português no corpo.

```bash
git checkout -b fix/13-hover-dropdown      # tipo/numero-resumo-em-kebab
git add -A
git commit -m "$(cat <<'EOF'
fix(menu): aplica o hover dos cartões do dropdown conforme o Figma

Adiciona a seta ↗ ao cartão do painel e o estado de hover/focus-visible
(fundo, sombra e revelação da seta), usando os tokens existentes.

Closes #13

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>
EOF
)"
```

Regras:

- **Nunca commite direto na branch padrão** (`main`) — crie a branch antes.
- Um commit por issue; o `Closes #N` no corpo liga os dois.
- Só rode `git commit`/`git push` quando o usuário tiver escolhido essa entrega na
  Fase 4. `git push` publica — precisa de OK explícito.
- Não use `git add -A` se houver arquivo não relacionado no working tree; confira com
  `git status` e adicione os caminhos específicos.

Arquivos que **não** entram no commit (já em `.gitignore`, confirme mesmo assim):
`bin/wp.config.sh`, `assets/seed/`, `debug.log`, PNG/JPG soltos na raiz.

---

## 4. Pull request

```bash
gh pr create --repo <r> \
  --base main --head fix/13-hover-dropdown \
  --title "fix: hover dos cartões do dropdown" \
  --body-file /tmp/pr-13.md
```

O corpo precisa referenciar a issue (`Closes #13`) para o merge fechá-la. Modelo em
[`entrega.md`](entrega.md).

Se o usuário não usa PR (o repositório hoje tem só `main` e nenhum PR), commit na branch
+ comentário na issue é entrega suficiente — confirme na Fase 4 em vez de impor o fluxo.

---

## 5. Ambiente local do tema

Independente do git, a validação roda no site local:

```bash
./bin/wp option get home            # URL do site
./bin/wp cliconnect seed            # se a issue mexeu em conteúdo
source bin/wp.config.sh && "$PHP_BIN" -l <arquivo.php>
```

`bin/wp.config.sh` não é versionado. Se faltar, o wrapper aborta com instrução — repasse
ao usuário (`cp bin/wp.config.example.sh bin/wp.config.sh`), não tente contornar.
