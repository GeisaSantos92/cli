# Medir a divergência

A imagem diz **onde olhar**. O número diz **se existe**. Toda divergência que for para o
relatório carrega uma medida de cada lado.

Isso não é rigor pelo rigor: nesta base, dois achados "óbvios na imagem" morreram na
medição — ver [Armadilhas](#armadilhas) ao final.

---

## Lado Figma — Plugin API

Um script de leitura via `use_figma` devolve as medidas exatas de qualquer nó. Invoque
`figma:figma-use` antes (pré-requisito do MCP) e peça **só o que vai usar** — a árvore
inteira estoura o limite de tokens.

**Caixa de um nó:**

```js
const n = await figma.getNodeByIdAsync("15273:41315");
return { w: Math.round(n.width), h: Math.round(n.height),
         x: Math.round(n.x), y: Math.round(n.y) };
```

**Tipografia de uma seção** — resolve quebra de linha divergente, que quase sempre é
`max-width` e não `font-size`:

```js
const sec = await figma.getNodeByIdAsync("15273:41317");
return sec.query('TEXT').map(t => ({
  txt: t.name.slice(0, 40), w: Math.round(t.width), size: t.fontSize,
  font: t.fontName.family + ' ' + t.fontName.style,
}));
```

**Componente repetido** (card, célula de grade, slot de esteira) — pegue a primeira
instância e leia `width`, `height` e o `paddingLeft`/`itemSpacing` do auto-layout. A
diferença entre duas instâncias vizinhas dá o gap.

Referências deste tema: os frames `content` de cada seção têm **1312 px em x=110**, dentro
de um frame de 1531 px. É a largura de conteúdo do layout.

---

## Lado site — DOM renderizado

`getBoundingClientRect()` para o que ocupa espaço, `getComputedStyle()` para o que foi
declarado. Peça os dois na mesma chamada:

```js
(() => { const g = s => { const e = document.querySelector(s); if (!e) return null;
  const c = getComputedStyle(e), r = e.getBoundingClientRect();
  return { w: Math.round(r.width), h: Math.round(r.height),
           fs: c.fontSize, lh: c.lineHeight, mw: c.maxWidth, pad: c.padding }; };
  return JSON.stringify({ titulo: g('.compliance__titulo'), grade: g('.compliance__grid') }, null, 1);
})()
```

Rode com o viewport na **largura do frame** (`resize_window` para 1531), senão os números
descrevem outra página.

Para a ordem e a altura das seções, que orientam onde cortar as fatias:

```js
[...document.querySelectorAll('main > *')].map(e => {
  const r = e.getBoundingClientRect();
  return e.className + ' ' + Math.round(r.top + scrollY) + '→' + Math.round(r.bottom + scrollY);
});
```

---

## Armadilhas

**`getBoundingClientRect()` devolve border-box.** O `.container` do tema mede 1392 px
porque inclui os 40 px de recuo de cada lado; o conteúdo são 1312 px, batendo com o Figma.
Antes de acusar largura errada, subtraia `padding` e `border` — ou leia
`getComputedStyle().width`, que é content-box por padrão.

**Medir forma por pixel escuro pega o conteúdo, não o contorno.** As bolhas de logo do
hero têm borda quase branca: uma varredura por luminância acha a arte do logo dentro
delas e devolve um diâmetro menor. Meça pela API e pelo DOM, nunca pelo bitmap.

**Texto dentro de asset não é bug de fonte.** Antes de culpar a Rajdhani por um acento
faltando, confira se o texto está no DOM ou embutido na imagem — as ilustrações fechadas
(`assets/img/section-*.png`) trazem os rótulos na própria arte, e um acento errado ali é
reexportação, não CSS.

**Glifo estranho pode ser o desenho da fonte.** O til achatado de "Integrações" e a
cedilha destacada de "segurança" aparecem **iguais no Figma** — é a Rajdhani. Compare o
mesmo glifo nos dois lados antes de abrir issue.

**Seção em branco no export pode ser animação.** Um vazio no PNG do Figma na altura de uma
seção implementada costuma ser um frame de motion (`Seção - Motion`), que só existe em
movimento. A Fase 1 revela: se o nó existe na árvore com altura compatível, a
implementação está certa.

**`get_screenshot` sem `maxDimension` devolve miniatura.** O padrão é 1024 px no lado
maior. Passe a altura real do frame.

**O painel do navegador embutido devolve screenshot velho ou em branco** quando está
oculto. Para captura confiável use o `captura.mjs`; o painel serve para rodar JavaScript
de medição, não para capturar.
