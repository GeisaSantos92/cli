# Landing pages com blocos (opção de desenvolvimento)

O starter é um Classic Theme: as páginas institucionais são 100% PHP/ACF. Este módulo é
a **exceção controlada** para quando o cliente precisa montar **landing pages** sozinho
no editor — blocos de seção do design system, sem abrir mão das regras do tema (sem
build, server-rendered, escaping estrito).

## Como ativar

1. Descomente em `functions.php`:
   ```php
   cliconnect_require( '/inc/blocks.php' );
   ```
2. Crie uma página e selecione o template **"Landing Page (canvas)"**
   (`page-templates/landing.php`).
3. No editor, o inserter mostra a categoria **"Seções do tema"** no topo.

Sem o template landing, nada muda nas demais páginas — a allowlist de blocos só atua no
canvas.

## Anatomia do padrão (sem build)

Cada bloco vive em `blocks/<nome>/` com três arquivos:

| Arquivo | Papel |
| --- | --- |
| `block.json` | Metadados: nome (`cliconnect/<nome>`), atributos, categoria, `render: file:./render.php` |
| `render.php` | Markup do **front**, server-rendered (escaping/`$attributes` como em qualquer template) |
| `editor.js` | UI do **editor** em JS puro (`wp.element.createElement`) — sem JSX, sem toolchain |

Regras do padrão:

- **`save: () => null`** nos blocos folha: nada de markup serializado no banco — o front
  é sempre o `render.php` (mudou o design? re-renderiza tudo, sem "block validation
  error").
- Blocos com filhos salvam **só** `InnerBlocks.Content`; o `render.php` do pai recebe os
  filhos prontos em `$content`.
- O **visual vive em classes CSS** (`assets/css/blocks.css`), compartilhadas entre
  `render.php` e `editor.js` — o editor mostra o mesmo resultado do front via
  `add_editor_style()`. Ajustes exclusivos do canvas: `assets/css/blocks-editor.css`.
- Registro central em `inc/blocks.php`: a lista `cliconnect_blocks_lista()` alimenta o
  registro dos scripts, dos block types e a allowlist.
- **Imagens por attachment ID** no atributo (`imagemId`); a URL salva é só para o
  preview do editor — o front resolve via `wp_get_attachment_image_url()`.

## Blocos de exemplo inclusos

Cobrem os três padrões de composição — duplique o mais próximo do que precisar:

| Bloco | Padrão que exemplifica |
| --- | --- |
| `cliconnect/hero` | Folha com RichText + MediaUpload (imagem de fundo) no InspectorControls |
| `cliconnect/stats` + `cliconnect/stat-item` | Pai/filho repetível: InnerBlocks com `allowedBlocks` + `parent` no filho (substitui o Repeater do ACF Pro no editor) |
| `cliconnect/secao-texto` | Wrapper de InnerBlocks livre: dá container/tipografia do tema aos blocos nativos |

## Como criar um bloco novo

1. Duplique o diretório de exemplo mais parecido em `blocks/<novo-nome>/`.
2. Ajuste `block.json` (name `cliconnect/<novo-nome>`, título, atributos).
3. Escreva o `render.php` (front) e o `editor.js` (espelhando as mesmas classes).
4. Estilize em `assets/css/blocks.css` com o prefixo `cliconnect-bl-`.
5. Adicione `<novo-nome>` em `cliconnect_blocks_lista()` (`inc/blocks.php`).

## Limites propositais

- A allowlist do canvas libera poucas seções + nativos básicos (`paragraph`, `heading`,
  `list`, `image`, `spacer`, `embed`) — pensados para viver dentro de "Seção de texto".
  Amplie com critério: cada bloco nativo liberado precisa renderizar bem no canvas.
- Isso **não** é FSE: header/footer/templates continuam PHP. Os blocos são só o
  conteúdo de landing pages.
- `supports.html: false` e `customClassName: false` mantêm o cliente dentro do design
  system.
