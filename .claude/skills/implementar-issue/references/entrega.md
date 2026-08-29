# Entrega — commit na `main`, comentário e fechamento

Modelos prontos. Adapte o conteúdo; mantenha a estrutura.

---

## 1. Mensagem de commit

```
fix(menu): aplica o hover dos cartões do dropdown conforme o Figma

Adiciona a seta ↗ ao cartão do painel e o estado de hover/focus-visible
(fundo, sombra e revelação da seta), usando os tokens existentes.

Closes #13

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>
```

- Primeira linha: `tipo(escopo): o que mudou`, no imperativo, ≤ 72 caracteres.
- Escopos usuais deste tema: `menu`, `home`, `rodape`, `header`, `seed`, `acf`, `css`,
  `cpt`, `enqueue`, `docs`.
- Corpo: **por que**, não o diff. Duas ou três linhas bastam.
- `Closes #N` liga o commit à issue (rastro; sem PR não fecha sozinho).
- O commit vai direto na `main` — nunca em branch de feature, nunca via PR.

---

## 2. Depois do commit — main local em dia

Fechamento obrigatório da Fase 7, antes de concluir e antes de pegar a próxima issue:

```bash
git pull --rebase
git push          # publica — só com OK do usuário
git status -sb    # limpo, sem "behind"
git log --oneline -3
```

Relate no fechamento o sha do commit e o estado da `main`: sincronizada com o remoto ou
à frente dele (push não autorizado). Nada de PR — este repositório não usa.

---

## 3. Comentário na issue

Sempre opcional e sempre com autorização — não existe PR para narrar a mudança:

```markdown
Implementado na `main` (`a14125e`).

**O que mudou**
- `inc/menu-walker.php` — seta ↗ nos cartões do painel.
- `assets/css/theme.css` — estado de hover e foco dos cartões.

**Como validar**
Abrir a home, passar o mouse sobre os cartões do menu "Plataforma" e conferir contra a
imagem da issue. Navegação por teclado tem o mesmo estado.

**Observação**
A captura estática não registra `:hover`; validei pelo CSS aplicado. Vale um olhar seu
no navegador antes de fechar.
```

Comentário é público para quem tem acesso ao repositório — poste só com autorização.

---

## 4. Fechar a issue

- Como não há PR, **nada fecha a issue sozinho** — nem o `Closes #N` do commit.
- Feche só com pedido explícito do usuário:
  ```bash
  gh issue close 13 --repo <r> --comment "Resolvido em <sha>."
  ```
- Issue que se revelou **inválida ou já resolvida**: não feche por conta própria.
  Relate ao usuário e sugira o rótulo (`invalid`, `duplicate`, `wontfix`) — a decisão é
  dele.

---

## 5. Fechamento da conversa

Termine sempre com, em texto:

1. **Issue e título** tratados.
2. **Arquivos alterados**, em lista, com uma linha do que mudou em cada.
3. **Como validou** — `php -l`, seed, captura, teste manual — e o que **não** deu para
   validar automaticamente (estados de `:hover`, e-mail, integração externa).
4. **O que ficou fora** e por quê.
5. **Estado do git e do GitHub** — sha do commit na `main`, se o push foi feito, se
   houve comentário, issue aberta ou fechada.
6. Um pedido explícito de conferência: *"dá uma olhada no resultado e me diz se pode
   fechar a issue"*.

Nada de "pronto e funcionando" sem ter olhado o resultado renderizado.
