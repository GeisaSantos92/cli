---
name: revisar-layout
description: >
  Compara uma página já implementada do tema CLI Connect com o frame do Figma que a
  originou e entrega um relatório priorizado das divergências — opcionalmente virando
  issues no GitHub. Mapeia as seções do frame, captura os dois lados na mesma largura,
  mede toda divergência antes de reportá-la e tria o que é erro do código, o que é
  decisão deliberada e o que é erro do próprio layout. Use quando pedirem "revisar o
  layout", "comparar com o Figma", "ficou fiel ao design?", "o que saiu diferente do
  layout", "auditoria visual da página", ou ao receber um link de frame do Figma junto
  de uma página que já existe.
---

# Revisar layout contra o Figma

Diferente de `criar-pagina`, aqui a página **já existe**: o produto é um diagnóstico, não
código. O trabalho de verdade é a **triagem** — separar divergência real de decisão
deliberada e de erro do próprio layout. Despejar tudo que parece diferente é pior que não
revisar: vira ruído e treina o usuário a ignorar o relatório.

## Critério: fidelidade de composição

O alvo é **o que existe, em que ordem, com que proporção e com que conteúdo**.

Altura total da página, altura de seção e ritmo vertical **não entram** — variação ali é
esperada. Medida que vale é horizontal (largura de coluna, `max-width`, largura de grade)
ou estrutural (quantidade, ordem, presença). Confirmado com o usuário nesta base.

---

## Fase 1 — Mapear as seções do frame

`get_metadata` no frame devolve **só o próprio frame**, sem filhos. Quem entrega a árvore
é um script de leitura via `use_figma` (invoque `figma:figma-use` antes — é pré-requisito
do MCP):

```js
const home = await figma.getNodeByIdAsync("15273:40478");
return home.children.map(c => ({ id: c.id, name: c.name, y: Math.round(c.y),
                                 h: Math.round(c.height), visible: c.visible }));
```

Descarte os `visible: false` — um frame do Figma costuma carregar versões antigas
empilhadas na mesma posição.

Monte a tabela **seção do Figma ↔ template-part**, comparando os nomes dos nós com
`front-page.php` (ou o `page-{slug}.php` correspondente) e `template-parts/`.

**Fim da fase:** toda seção visível do frame está pareada com um template-part, ou
marcada como não implementada. Toda seção renderizada que não tem par no frame também
está anotada.

---

## Fase 2 — Capturar os dois lados na mesma largura

Use a **largura do frame** como viewport, para que as duas imagens fiquem 1:1 e
comparáveis fatia a fatia.

```bash
# Site — reaproveita o script de criar-pagina, num viewport só
node .Codex/skills/criar-pagina/scripts/captura.mjs \
  "http://cli.local/" /tmp/revisao-home --viewports=1531

# Figma — get_screenshot com maxDimension = altura real do frame, depois fatiar
bash .Codex/skills/revisar-layout/scripts/fatiar-figma.sh \
  "<url do get_screenshot>" /tmp/revisao-home/figma
```

Os dois fatiam de 1600 px por padrão, então a fatia N de um lado corresponde à fatia N do
outro. Ao mudar a altura, mude nos dois.

`get_screenshot` **sem `maxDimension`** devolve o lado maior em 1024 px: um frame de 14 mil
px vira uma tira ilegível. Passe a altura real que a Fase 1 já revelou.

Leia as fatias **em pares** — mesma faixa do Figma e do site, uma seção por vez.

---

## Fase 3 — Medir toda divergência

A imagem diz **onde olhar**; o número diz **se existe**. Todo item que for para o
relatório carrega uma medida de cada lado: Plugin API do Figma de um, DOM renderizado do
outro.

Como tirar cada número, e as armadilhas que já produziram falso positivo neste tema:
[`references/medir.md`](references/medir.md).

**Fim da fase:** nenhum item sem número dos dois lados. Achado que resistiu à imagem mas
não sobreviveu à medição é descartado ali mesmo.

---

## Fase 4 — Triar

Cada achado medido cai em uma de quatro caixas — só a primeira vira issue:

| Caixa | Destino |
|---|---|
| **Divergência real** | relatório |
| **Decisão deliberada** | relatório, como "confirmar", com o commit/issue que decidiu |
| **Erro do próprio layout** | relatório, como "manter o site" |
| **Fora de critério** | descartado |

Como reconhecer cada uma, e onde procurar a decisão deliberada (`git log` do arquivo é o
primeiro lugar): [`references/triagem.md`](references/triagem.md).

---

## Fase 5 — Relatar

Agrupe por severidade, estruturante primeiro. Para cada item:

```
ESTRUTURAL · Compliance · template-parts/home/compliance.php
  Figma:    10 selos em 5×2, grade de 1059 px
  Site:     11 selos em 3 linhas, grade de 1312 px
  Causa:    cliconnect_posts('cli_selo') lista todos, sem filtro
```

Feche com duas listas: o que muda e **o que foi conferido e está correto**. A segunda
evita retrabalho na próxima revisão — é onde entram os itens que pareciam divergência e
não eram.

Se a página estiver fiel, diga isso e mostre a cobertura: "sem achados" só tem valor se o
usuário souber o que foi verificado.

---

## Fase 6 — Abrir issues (só com aprovação)

Pergunte com `AskUserQuestion` se o relatório vira issue. Se sim, o formato — issue mãe
com sub-issues, deep link do node-id em cada uma, plano de implementação por issue —
está em [`references/issues.md`](references/issues.md).

---

## Linha de base conhecida

A revisão completa da home está em
[#108](https://github.com/GeisaSantos92/cli/issues/108), com a lista "Conferido e correto
— não mexer". Consulte antes de reportar; um achado **novo** em relação a ela merece mais
atenção que os já conhecidos.

Os dois que mais custam tempo, porque parecem óbvios na imagem e não são:

- **Bolhas de logo do hero** — 74×74 no Figma e no site. Medir o círculo por pixel escuro
  pega a arte do logo, não a borda quase branca.
- **Largura do container** — `.container` mede 1392 px porque o `getBoundingClientRect()`
  inclui os 40 px de recuo de cada lado. O conteúdo são 1312 px, batendo com os frames
  `content` do Figma. `--largura-conteudo` está certo.
