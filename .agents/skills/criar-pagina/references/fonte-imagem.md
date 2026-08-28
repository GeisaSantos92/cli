# Fonte: screenshot / print do layout

Sem Figma, a imagem é **a única fonte de verdade** — de layout *e* de conteúdo. Leia-a
com atenção antes de escrever qualquer arquivo.

---

## 1. Ler a imagem

Use `Read` no arquivo (PNG/JPG). Se o usuário mandou vários prints (um por dobra),
leia todos e monte a ordem. Se o print estiver muito longo e o texto ficar ilegível,
peça um recorte por seção em vez de adivinhar.

Extraia, nesta ordem:

1. **Seções**, de cima para baixo, com um nome de assunto em kebab-case.
2. **Todo o texto, transcrito literalmente** — acentuação, maiúsculas/minúsculas,
   pontuação e quebras de linha propositais. Isso vira o seed.
3. **Hierarquia tipográfica**: qual é o `h1`, quais são `h2`, o que é eyebrow, o que é
   corpo, o que é legenda. Compare o tamanho relativo com a escala do tema
   (`--tam-h1` 80 / `h2` 60 / `h3` 48 / `h4` 36 / `h5` 28 / `h6` 20 / corpo 18–16).
4. **Cores**, casadas com os tokens existentes (ver `padroes-tema.md`). Um azul de botão
   no print é `--cor-primaria`; um fundo levemente azulado é `--cor-fundo-suave`.
   **Não amostre pixel para criar cor nova** — o print tem compressão e o tema já tem
   a paleta certa.
5. **Ritmo vertical**: espaçamento entre seções ≈ `--secao-espacamento`; entre título e
   texto, 16–24px; entre texto e botão, 24–36px. Ajuste fino fica para a Fase 5.
6. **Imagens e ícones**: liste cada um e classifique (ilustração fechada → `assets/img/`;
   conteúdo → `assets/seed/`). Ícone de interface provavelmente já existe em
   `cliconnect_icone()` — confira a lista antes de pedir asset.

---

## 2. Fechar as lacunas com o usuário

Print não mostra tudo. Antes de implementar, pergunte o que faltar (junte numa única
mensagem):

- **Destino dos links/botões** — a URL não aparece no print.
- **Estados** (hover, foco, ativo) — assuma o padrão do tema e avise.
- **Responsivo** — o print costuma ser só desktop. Proponha o comportamento
  (empilhar em ≤768px, grade 2 colunas em tablet) e siga; não trave a entrega nisso.
- **Textos cortados/ilegíveis** — liste os trechos e peça o texto.
- **Listas que crescem** — "esses 6 cards são fixos ou o cliente vai adicionar mais?"
  A resposta decide entre campos numerados e CPT.

---

## 3. Assets

Se o usuário respondeu na Fase 0 que **já exportou os assets**, liste a pasta e case
cada arquivo com uma seção pelo nome. Se não houver assets exportados:

- Recortar imagem de dentro de um print é último recurso: perde qualidade e não escala.
  Peça o arquivo original ao usuário.
- Enquanto não vier, implemente a seção com o campo de imagem vazio — o template-part já
  degrada bem (`return` cedo / `cliconnect_imagem_tema()` devolve string vazia). Registre
  a pendência na entrega.

---

## 4. Fidelidade possível

Diga com clareza, na entrega, o que foi **inferido** do print: espaçamentos exatos,
pesos de fonte, comportamento responsivo e qualquer estado não visível. Isso guia a
validação do usuário na Fase 6 em vez de virar retrabalho depois.
