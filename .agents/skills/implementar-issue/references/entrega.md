# Entrega — commit, PR, comentário e fechamento

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
- `Closes #N` liga o commit à issue.

---

## 2. Corpo do PR

```markdown
Closes #13

## O que a issue pedia
O dropdown do menu não seguia o Figma: faltava o estado de hover dos cartões.

## O que foi feito
- `inc/menu-walker.php` — seta ↗ (`cliconnect_icone('seta-nordeste')`) em cada cartão.
- `assets/css/theme.css` — hover/`:focus-visible` do `.nav-cartao`: fundo, sombra e
  revelação da seta, com os tokens existentes.

## Como validar
1. `./bin/wp cliconnect seed` (não necessário — não houve mudança de conteúdo).
2. Abrir a home e passar o mouse sobre os cartões do painel "Plataforma".
3. Navegar por teclado (Tab) — o mesmo estado aparece no foco.

## Fora de escopo
Mega menu de "Soluções" (não citado na issue).
```

---

## 3. Comentário na issue

Quando a entrega for por commit direto na branch, sem PR:

```markdown
Implementado em `fix/13-hover-dropdown` (`a14125e`).

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

- **Com PR**: não feche à mão. O `Closes #N` fecha no merge.
- **Sem PR**: só com pedido explícito do usuário.
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
5. **Estado no GitHub** — branch criada? commit? PR? comentário? issue aberta ou
   fechada?
6. Um pedido explícito de conferência: *"dá uma olhada no resultado e me diz se pode
   fechar a issue"*.

Nada de "pronto e funcionando" sem ter olhado o resultado renderizado.
