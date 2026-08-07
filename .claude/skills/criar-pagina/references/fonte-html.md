# Fonte: template HTML

Converter um template HTML (comprado, entregue por terceiro ou gerado) em página do
tema. A entrega **não é o HTML embrulhado no WordPress** — é uma página no padrão do
tema, construída a partir do que o HTML descreve.

---

## 1. Inventariar antes de converter

```bash
ls -R caminho/do/template | head -60
```

Classifique o que veio:

| Encontrou | Destino |
|---|---|
| `index.html` / `pagina.html` | fonte de estrutura e texto |
| `css/*.css` próprio do template | **não copie**; serve de referência de medidas |
| CSS de framework (Bootstrap, Tailwind CDN) | descartar — o tema não usa framework |
| `js/*.js` de comportamento | avaliar caso a caso; jQuery e libs → descartar |
| `img/*` | classificar: ilustração fechada → `assets/img/`; conteúdo → `assets/seed/` |
| fontes (`fonts/`, `<link>` do Google) | descartar — o tema tem as suas, auto-hospedadas |

Renderize o HTML original para ter referência visual da Fase 5:

```bash
npx --no-install playwright screenshot --full-page --viewport-size="1440,900" \
  "file:///caminho/absoluto/index.html" referencia-html.png
```

---

## 2. Ler o HTML como *conteúdo*, não como markup

Do arquivo, extraia:

1. **Seções** — normalmente já delimitadas por `<section>`/`<div class="...">` com
   nomes úteis. Uma seção = um template-part.
2. **Texto real** de cada seção — títulos, parágrafos, itens de lista, rótulos e
   `href` de botões. Isso vira o seed, literalmente.
3. **Repetições** — um bloco de card repetido 6 vezes no HTML é o sinal mais claro de
   CPT (ou campos numerados, se o conjunto for fixo).
4. **Medidas** do CSS do template (largura de container, escala tipográfica, raios,
   espaçamentos) — só para **comparar** com os tokens do tema.

---

## 3. Traduzir markup e CSS

Regras de conversão:

- **Reescreva o HTML.** Descarte `div` de framework, wrappers de grid (`row`/`col-md-6`),
  classes utilitárias. Use HTML semântico + as classes globais do tema (`.container`,
  `.secao`, `.botao`, `.eyebrow`) e BEM em português para o específico.
- **Cores, fontes e raios**: mapeie para tokens (`padroes-tema.md`). O template traz a
  paleta dele; a paleta que vale é a do tema. Se o cliente comprou o HTML *pela*
  identidade visual dele, isso é uma decisão do usuário — pergunte antes.
- **Ícones**: `<i class="fa fa-...">` → `cliconnect_icone()`. Se o ícone não existir,
  acrescente o `path` ao array em `inc/icons.php` (estilo Material Symbols, viewBox 24).
  Não instale biblioteca de ícones.
- **Imagens**: `<img src="img/foto.jpg">` → campo ACF de imagem + `wp_get_attachment_image()`,
  ou `cliconnect_imagem_tema()` se for arte fechada. Nunca `src` hardcoded.
- **JS**: só traga comportamento que a página realmente precise, em vanilla, dentro de
  `assets/js/theme.js` (ou um arquivo próprio enfileirado por contexto). jQuery, sliders
  e libs de animação **não entram** — o tema é de baixa dependência. Acordeão e toggles
  já existem em `theme.js`: reaproveite o padrão de `aria-expanded`/`data-*`.
- **Formulário**: HTML estático traz `<form action="...">` que não funciona no WP.
  Pergunte ao usuário como o formulário deve funcionar antes de implementar qualquer
  coisa — não invente endpoint nem plugin.
- **`<head>`**: nada dali é reaproveitado. Meta tags são do `wp_head()`; CSS/JS entram
  por `wp_enqueue_*`.

---

## 4. Riscos a sinalizar

Antes de implementar, avise o usuário se encontrar:

- **Licença/crédito** no HTML (rodapé "template by...") — não replique sem confirmar.
- **CDNs e trackers** embutidos — serão removidos (o tema tem `inc/analytics.php`).
- **Animações pesadas** (AOS, WOW, parallax) — proponha equivalente em CSS ou remoção.
- **Diferença grande de identidade** entre o template e o design system do CLI Connect —
  essa é uma decisão de projeto, não sua.
