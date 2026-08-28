# Estender ou criar uma seção de solução

Alcançado quando a rodada **não** é "reutilizar": o frame do Figma pede algo que o
catálogo de `SKILL.md` não entrega exatamente como está.

---

## 1. Estender antes de criar

Um frame que parece novo quase sempre é um componente existente com um pedaço a mais.
Antes de escrever template-part novo, abra o `template-parts/solucao/` candidato e
pergunte qual destes é o caso:

| Sintoma no Figma | Extensão |
|---|---|
| Um card a mais do que o laço percorre | Amplie o limite do `for` e acrescente os campos `_{n}_*` |
| Um eyebrow/corpo que o template não renderiza | Campo novo + bloco condicional no template-part |
| Mesma estrutura, cor/espaçamento diferente | Ajuste o token no CSS — confira antes se o desenho não é o correto e o tema é que está defasado |
| Mesmo card, outra seção | O card-global já existe em Pilares e Casos de Uso; copie a marcação, não invente outra |

Exemplos reais da landing de Serviços Financeiros: Casos de Uso ia até 5 cards e o
desenho pedia 6 (virou `solucao_casos_6_*` e laço até 6); Pilares não tinha eyebrow
(virou `solucao_pilares_eyebrow`). Nenhum dos dois virou seção nova.

**Extensão é compartilhada.** O mesmo template-part serve todas as soluções, então uma
mudança de cor ou espaçamento respinga no Salesforce e nas demais. Quando o ajuste é
fidelidade ao design system (um token errado, um respiro fora do ritmo `120px 80px`),
aplique e registre no commit. Quando é específico desta landing, resolva com campo ou
modificador de classe, não alterando o padrão de todo mundo.

---

## 2. Criar uma seção nova

Quatro arquivos, nesta ordem:

**a) Template-part** — `template-parts/solucao/<slug>.php`:

```php
<?php
/**
 * Solução — <Nome da seção>.
 *
 * <Uma frase sobre o que a seção mostra.>
 *
 * Campos ACF (group_cli_solucao, aba "N · <Nome>"):
 *   solucao_<prefixo>_*.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'solucao_<prefixo>_titulo' );

if ( ! $titulo ) {
	return;
}
?>
<section class="s<x>-<slug>" aria-label="<?php esc_attr_e( '<Rótulo>', 'cli' ); ?>">
	<div class="container">
		…
	</div><!-- .container -->
</section><!-- .s<x>-<slug> -->
```

O `return` cedo é o que torna a seção invisível nas soluções que não a preenchem — sem
ele, a seção aparece vazia em toda a base.

**b) Registro** — acrescente o slug ao array `$cliconnect_secoes` em
`single-cli_solucao.php`, **na posição que ele ocupa no Figma**.

**c) Campos ACF** — grupo `group_cli_solucao` em `inc/acf-fields-cpt.php`, aba nova na
posição correspondente. Chaves `field_solucao_<prefixo>_*`, nomes `solucao_<prefixo>_*`.

**d) CSS** — bloco novo em `assets/css/page-solucao.css`, com o marcador
`/* --- N. Seção <Nome> --- */` na mesma posição, e só tokens de `theme.css`.

---

## 3. Renumeração (o passo que mais escapa)

Inserir uma aba no meio renumera todas as seguintes. Quatro lugares guardam esses
números e **saem de sincronia em silêncio** — o painel do ACF fica com duas abas "5", os
comentários passam a apontar para a seção errada:

1. `'label' => 'N · Nome'` das abas em `inc/acf-fields-cpt.php`.
2. Os comentários `// ----- Aba: N · Nome ---` no mesmo arquivo.
3. Os marcadores `/* --- N. Seção Nome --- */` em `assets/css/page-solucao.css`.
4. Os docblocks `Campos ACF (…, aba "N · Nome")` nos `template-parts/solucao/*.php`.

Confira os quatro depois de renumerar:

```bash
grep -n "'label'.*·" inc/acf-fields-cpt.php
grep -n "// ----- Aba:" inc/acf-fields-cpt.php
grep -n "^/\* --- [0-9]" assets/css/page-solucao.css
grep -n 'aba "' template-parts/solucao/*.php
```

Os quatro devem contar a mesma história, na ordem de `$cliconnect_secoes`.

---

## 4. Escolher o tipo de campo

- **Lista de tamanho fixo** (3 métricas, 6 cards) → campos numerados
  `solucao_<prefixo>_{n}_*`, lidos com `cliconnect_lista_numerada_pagina()`.
- **Lista que cresce ou já existe como conteúdo** → `relationship` para o CPT
  correspondente. Já em uso: `solucao_logos_clientes` → `cli_cliente`,
  `solucao_faq_itens` → `cli_faq`. Use `return_format => 'id'` (ou `'object'` quando o
  template precisa do título e do conteúdo, como no FAQ).
- **Ícone** → campo `image` com `return_format => 'id'` e `mime_types => 'svg, png'`,
  renderizado como `mask-image` sobre o quadrado em degradê.

ACF Free não tem Repeater. Se a tentação for um repetidor, a resposta é CPT.
