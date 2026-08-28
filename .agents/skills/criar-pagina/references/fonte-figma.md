# Fonte: Figma

Extrair um frame do Figma é a etapa **mais cara em tokens** do fluxo. A ordem abaixo
existe para gastar o mínimo possível sem perder fidelidade.

> A skill `figma:figma-design-to-code` é pré-requisito obrigatório do MCP antes de
> chamar `get_design_context`. Invoque-a quando for usar essa ferramenta.

---

## Regra de ouro: assets exportados à mão mudam tudo

Se na Fase 0 o usuário respondeu que **já exportou os assets**:

1. Liste a pasta informada (`ls`) e registre os nomes dos arquivos.
2. **Não chame `download_assets`. Não peça `get_screenshot` de nós de imagem.**
   Não navegue pela árvore de camadas só para descobrir o que é imagem.
3. No `get_design_context`, ignore tudo que for `IMAGE`/`VECTOR` grande — você já tem
   o arquivo. Foque em **texto, hierarquia, espaçamento e cor**.
4. Case cada asset com a seção pelo nome do arquivo; se um nome estiver ambíguo,
   pergunte ao usuário em vez de re-explorar o Figma.

Esse único desvio costuma cortar a maior parte do custo do frame.

Se o usuário respondeu **"exportar do Figma"**, baixe só o que for realmente imagem
(ilustração fechada, foto, logo) — ícone de interface é `cliconnect_icone()`, não asset.

---

## Sequência recomendada

1. **`get_screenshot` do frame inteiro** — uma imagem, barata, e é a sua referência
   visual para toda a Fase 5. Guarde o caminho.
2. **`get_metadata`** do frame — devolve a árvore com nome/tipo/posição/tamanho dos nós,
   sem o CSS. É o suficiente para **listar as seções** e decidir o recorte em
   template-parts. Faça o recorte aqui, antes de puxar qualquer detalhe.
3. **`get_variable_defs`** — variáveis do arquivo. Compare com os tokens de
   `assets/css/theme.css`: como os tokens do tema **já espelham** o Figma, isso serve
   para confirmar o mapeamento (`#3551f2` → `--cor-primaria`), não para criar tokens
   novos.
4. **`get_design_context` por seção**, uma seção por vez, usando o `nodeId` da árvore —
   **nunca no frame inteiro**. Extraia dali: textos, hierarquia, espaçamentos, cores,
   estados.
5. Pare de puxar contexto assim que tiver o necessário para a seção. Se restou dúvida
   de 4px de padding, resolva na comparação visual da Fase 5 — sai mais barato.

---

## O que fazer com o CSS que o Figma devolve

O `get_design_context` devolve CSS gerado por máquina. **Não copie.** Traduza:

| Figma devolve | Escreva |
|---|---|
| `color: #3551f2` | `color: var(--cor-primaria)` |
| `font-family: Rajdhani; font-size: 60px` | `h2` (já é `--tam-h2`) — não redeclare |
| `font-size: 48px` num título de seção | `font-size: var(--tam-h3)` |
| `border-radius: 12px` | `border-radius: var(--raio-card)` |
| `padding: 120px 0` | `padding-block: var(--secao-espacamento)` |
| `width: 1312px` | `.container` |
| `gap: 24px` / `32px` | literal mesmo — só espaçamento interno não é tokenizado |
| posição absoluta de tudo | Flex/Grid; absoluto só para elemento decorativo |

Tudo que for **texto** vira campo ACF (ou CPT), nunca string no template.

---

## Recorte em seções

Uma seção do Figma = um template-part. Sinais de corte: mudança de cor de fundo, faixa
de largura total, título de seção (`<h2>`), bloco que se repete.

Ao nomear, use o **assunto**, não a posição: `recursos.php`, não `secao-3.php` — é a
convenção de `template-parts/home/`.

Antes de escrever a seção do zero, procure equivalente na home: hero centralizado,
texto+imagem alternado, grade de cards, esteira de logos e acordeão **já existem** em
`template-parts/home/` com CSS em `front-page.css`. Copiar a estrutura (e adaptar os
nomes BEM) é mais barato e mais consistente do que inventar.

---

## Fidelidade vs. sistema

Quando o frame diverge do design system (um azul levemente diferente, um H2 de 58px),
a decisão padrão é **seguir o sistema** e registrar a divergência na entrega. Exceção:
quando a diferença for claramente intencional e estruturante (uma seção com fundo
escuro que só existe nessa página) — aí crie a variante, com nome BEM e comentário.

Nunca adicione token novo em `:root` por causa de uma página. Se for mesmo necessário,
proponha ao usuário antes.
