# Regras — como triar cada indício

Para cada regra: **o que é**, **como reconhecer violação real**, **exceções legítimas já
verificadas neste tema** e **como corrigir**.

Fonte das regras: `CLAUDE.md`, `docs/code-standards.md`, `docs/architecture.md`.

---

## BLOQUEIO — segurança e erro fatal

### R01 · Arquivo PHP sem a guarda `ABSPATH`

```php
if ( ! defined( 'ABSPATH' ) ) { exit; }
```

**Violação:** qualquer `.php` do tema sem a guarda — o arquivo pode ser executado por
acesso direto.
**Exceção:** nenhuma. Todo arquivo do tema leva a guarda, inclusive `render.php` de bloco.
**Correção:** adicionar logo após o docblock.

### R02 · `echo` de variável sem escape

**Violação real:** a variável carrega dado do banco, do usuário ou do ACF.
```php
<h2><?php echo $titulo; ?></h2>          → esc_html( $titulo )
<a href="<?php echo $url; ?>">           → esc_url( $url )
```

**Exceções legítimas (todas presentes hoje):**

| Padrão | Por quê |
|---|---|
| `<?php echo $passada ? 'aria-hidden="true"' : ''; ?>` | ternária entre **strings literais** do próprio código — não há dado externo |
| `<?php echo $imagem; // wp_get_attachment_image escapa. ?>` | HTML montado por função do Core que já escapa |
| `<?php echo $cartao; // phpcs:ignore ... ?>` | montado por `cliconnect_imagem_tema()`, que escapa atributo por atributo |
| `echo cliconnect_icone( $nome )` | SVG estático de lista fechada, validado por chave |

O que separa exceção de violação: **a origem do valor**, não a forma do `echo`. Se a
string é literal no código ou já passou por função de escape, está certo — e deve haver
um comentário dizendo isso. Se não houver, o ajuste é **adicionar o comentário**.

### R03 · PHP cru de rede

**Violação:** `curl_init()`, `file_get_contents('https://...')`.
**Correção:** `wp_remote_get()` / `wp_remote_post()` + `wp_remote_retrieve_body()`, com
cache em Transient quando fizer sentido.
**Exceção:** nenhuma no runtime do tema.

### R04 · `style=` inline montado em PHP

**Violação:** cor, tamanho ou espaçamento no atributo — aparência pertence ao CSS.
**Exceção legítima:** valor que **só existe em runtime** e não tem como virar classe —
o caso real é `blocks/hero/render.php:28`:
```php
style="background-image:url('<?php echo esc_url( $imagem ); ?>');"
```
URL de imagem escolhida pelo editor, escapada com `esc_url()`. Correto.
**Correção quando for violação:** classe CSS; se precisar de valor dinâmico, custom
property inline (`style="--hero-img:url(...)"`) e o resto no arquivo CSS.

---

## PADRÃO — regras do projeto

### R05 · Dado externo sem coalescência nula

PHP 8 dá Fatal Error ao passar `null` para função de string.

**Violação:** `esc_html( get_field( 'x' ) )`, `trim( get_theme_mod( 'y' ) )`.
**Correção:** `get_field( 'x' ) ?? ''`.

**Exceções legítimas:**

| Padrão | Por quê |
|---|---|
| `if ( get_field( 'em_andamento', $id ) )` | contexto booleano — `null` é falsy, não quebra |
| `get_theme_mod( 'nav_menu_locations', array() )` | o **segundo argumento já é o default** — é o padrão correto |

Ou seja: o que importa é se o `null` chega numa função de string.

### R06 · Função de topo sem prefixo

**Violação:** `function formatar_data()` — colide com Core/plugin.
**Correção:** `function cliconnect_formatar_data()`. Vale para hooks e handles
(`cliconnect-`), constantes (`CLICONNECT_`) e `@package Cliconnect`.
**Exceção:** métodos de classe (indentados, não pegos pela regra).

### R07 · Cor literal fora dos tokens

O `:root` de `theme.css` (linhas 1–62) é a única fonte de cor. O script já ignora
branco, preto, alfas de branco/preto e linhas de comentário.

**Violação real — três tipos:**

1. **Token existente escrito cru** — `#3551f2` em vez de `var(--cor-primaria)`,
   `#0041ba` em vez de `var(--cor-primaria-escura)`. Correção trivial e sempre válida.
2. **Cor de marca sem token** — `#12b76a` + `rgba(18, 183, 106, .12)` em
   `front-page.css` (verde de status, usado duas vezes, sem token; `--cor-positivo` é
   `#02542d`, outro verde). Correção: **propor** `--cor-sucesso` ao usuário — criar
   token é decisão de design system, não conserto automático.
3. **Cinza/azul avulso** — `#667085`, `#616161`, `#cfdeff`, `#0f1c3f`. Avalie caso a
   caso: usado 1× e comentado, tolerável; repetido, vira token.

**Exceções legítimas:**

| Padrão | Por quê |
|---|---|
| `rgba(53, 81, 242, 0.28)` | é `--cor-primaria` com alfa; CSS não aplica alfa sobre `var()` de hex sem `color-mix()`. Aceite, mas confira se o RGB bate com o token |
| `#25d366` | verde do WhatsApp — cor de **marca externa**, não do design system |
| `#616161 /* Cor não tokenizada no Figma. */` | literal **com justificativa** — é o padrão correto |
| paradas de gradiente e sombras | tonalidades intermediárias que não são tokens |

### R08 · `<link>`/`<script>` cru no HTML

**Violação:** tag em `header.php`/`footer.php`/template.
**Correção:** `wp_enqueue_style()` / `wp_enqueue_script()` em `inc/enqueue.php`.
**Exceção:** `<link rel="preload">` de fonte impresso por
`cliconnect_preload_fonts()` no `wp_head` — preload não é enfileirável.

### R09 · Text-domain diferente de `cli`

**Violação:** `__( 'Texto', 'outro-dominio' )`. Quebra a tradução silenciosamente.
**Correção:** trocar para `'cli'`.

### R10 · Módulo em `inc/` não carregado

**Violação:** arquivo em `inc/` que `functions.php` nunca faz `cliconnect_require()` —
código morto, ou funcionalidade que o usuário acha que está ligada e não está.

**Caso conhecido:** `inc/blocks.php`. É **intencional** — o `SETUP.md` manda descomentar
por projeto, e a landing com blocos ainda não foi ativada. Não "corrija" sem perguntar;
o que cabe é confirmar se ainda faz sentido manter dormente.

### R11 · `require`/`include` direto em `functions.php`

**Violação:** carregar módulo sem passar por `cliconnect_require()` (que checa
existência antes).
**Exceção:** `functions.php:26` — é o `require $path;` **dentro da própria**
`cliconnect_require()`. Falso positivo por construção.

### R12 · `<img src>` hardcoded

**Violação:** perde `srcset`/`sizes` e não é editável.
**Correção:** `wp_get_attachment_image( $id, 'tamanho' )` para conteúdo, ou
`cliconnect_imagem_tema( 'arquivo.png' )` para arte do tema.

**Caso conhecido:** `template-parts/rodape-agencia.php:53` carrega o logo direto de
`agenciar8.com.br`. Não é violação de regra, mas é **dependência externa**: requisição a
outro domínio, sem cache do site, quebra se o domínio sair do ar. Vale sugerir mover o
arquivo para `assets/img/` — decisão do usuário.

### R13 · Enqueue sem `cliconnect_asset_version()`

**Violação:** arquivo que chama `wp_enqueue_style/script` sem usar o helper de
cache-busting — asset velho fica preso no cache do navegador.
**Correção:** `cliconnect_asset_version( '/assets/css/arquivo.css' )` no 4º argumento.

### R14 · `declare(strict_types=1)`

**Violação:** em arquivo que liga em hooks do WP — Core e plugins passam tipos
inesperados e o strict vira Fatal Error.
**Correção:** remover.

---

## ATENÇÃO — indícios, não vereditos

### R15 · `!important`

**Violação:** usado para vencer especificidade — sintoma de CSS mal estruturado.
**Exceção legítima:** dentro de `@media (prefers-reduced-motion: reduce)`
(`theme.css:322–324`) — é o idioma padrão da regra, que precisa mesmo sobrepor tudo.

### R16 · `TODO` / `FIXME` / `XXX` / `HACK`

Não é violação. Liste como pendência e pergunte se ainda vale.

### R17 · Asset órfão em `assets/img/`

Arquivo que nenhum PHP/CSS/JS referencia. Pode ser sobra de layout antigo — ou pode ser
usado por nome montado dinamicamente. Confira antes de sugerir remoção; **nunca apague
por conta própria**.

### R18 · Texto fixo em template

Heurística: texto acentuado longo fora de função de tradução. Mira o front-end — o
script já ignora `inc/cli/` (seed) e `inc/acf-fields-*` (rótulos do painel), que são
exatamente onde o texto **deve** estar.

**Violação:** conteúdo institucional escrito no template. Correção: virar campo ACF +
valor no seed.
**Exceção:** rótulo de interface (`esc_html_e( 'Integrações', 'cli' )`) e título de
archive que não é editável — texto de interface fica em função de tradução, não em ACF.

---

## O que o grep não vê

Quando o escopo permitir, verifique à mão:

- `template-parts/` sem `return` cedo → seção renderiza casca vazia.
- CSS de página enfileirado sem condição em `inc/enqueue.php` → peso em toda página.
- Campo ACF criado pelo painel → sai do versionamento (checar
  `wp post list --post_type=acf-field-group`).
- Lista que cresce em campo numerado em vez de CPT.
- Dado global (telefone, redes) em ACF em vez do Customizer.
- Menu criado por código sem sincronizar `polylang['nav_menus']` → front vazio
  (ver skill `traduzir-polylang`).
