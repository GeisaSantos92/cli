---
name: criar-cpt
description: >
  Cria um Custom Post Type novo no tema CLI Connect, no padrão do projeto: registro em
  inc/cpt.php, grupo ACF local em inc/acf-fields-cpt.php, taxonomia se precisar,
  templates de archive/single, card em template-parts, CSS por contexto e método de seed
  idempotente. Como o ACF Free não tem Repeater, toda lista que cresce vira CPT — este é
  o fluxo mais repetido do tema. Use quando pedirem "criar um CPT", "novo tipo de post",
  "preciso de uma listagem de X", "cadastrar depoimentos/serviços/membros", "isso vai
  virar um repetidor".
---

# Criar um CPT

No CLI Connect o CPT é o **substituto do Repeater** (o ACF Free não tem). A regra do
`CLAUDE.md`: *toda lista que pode crescer vira CPT; só conjunto de tamanho fixo
(métricas, departamentos) usa campos numerados*.

Já existem 7 CPTs (`inc/cpt.php`) — o novo deve parecer irmão deles, não primo distante.

---

## Fase 0 — Perguntar antes (OBRIGATÓRIA)

Antes de decidir qualquer coisa, **confirme que é mesmo um CPT**:

- Lista que cresce com o tempo, o cliente adiciona/remove itens → **CPT**.
- Conjunto fixo que nunca muda de tamanho (3 métricas, 6 departamentos) → **campos
  numerados** no grupo ACF da página. Não crie CPT.
- Dado único global (telefone, logo, redes) → **Customizer**.
- Um CPT existente já serve? (`cli_case`, `cli_integracao`, `cli_cliente`, `cli_evento`,
  `cli_faq`, `cli_selo`, `cli_agente`) → reutilize.

Depois, uma rodada de `AskUserQuestion` com o que faltar:

| # | Pergunta | Opções |
|---|---|---|
| 1 | Os itens têm página própria (URL, SEO, compartilhável)? | **Não — só catálogo** (alimenta seções; `public => false`) · **Sim — com archive e single** (como `cli_case`) |
| 2 | Precisa de taxonomia para agrupar/filtrar? | **Não** · **Sim, hierárquica** (categoria) · **Sim, plana** (tag) |
| 3 | Que campos cada item tem? | listar (texto, imagem, link, seleção, relação) — "Outra" para descrever |
| 4 | Já existe conteúdo real para o seed? | **Sim** (usuário fornece) · **Não, usar exemplos** |

Nome: peça **singular e plural em português**, e identifique o **gênero** — o helper
`cliconnect_cpt_labels( $singular, $plural, 'm'|'f' )` monta ~12 labels com a
concordância correta. Errar aqui gera "Nenhum Integração encontrado" no painel.

Slug do post type: `cli_{nome}`, sempre singular, sem acento
(`cli_depoimento`, não `cli_depoimentos`).

---

## Fase 1 — Plano (aprovação)

Apresente antes de escrever:

```
CPT:        cli_depoimento — "Depoimento" / "Depoimentos" (m)
Visibilidade: catálogo (public => false, sem archive)
Suporta:    title, thumbnail, page-attributes
Campos ACF: cargo (text), empresa (text), texto (textarea), nota (number)
Taxonomia:  não
Arquivos:   inc/cpt.php · inc/acf-fields-cpt.php · template-parts/card-depoimento.php
            inc/cli/seed.php (criar_depoimentos)
Consome:    template-parts/home/depoimento.php (seção existente)
```

Inclua o que **não** vai ser feito (ex.: "sem single/archive, porque é catálogo").
Só siga depois do OK.

---

## Fase 2 — Implementar

Ordem fixa. Moldes prontos em [`references/moldes.md`](references/moldes.md).

1. **`inc/cpt.php`** — `register_post_type()` dentro de
   `cliconnect_register_post_types()`, no fim da lista, com `menu_position` seguinte ao
   último (hoje o maior é 27). Atualize também o docblock do topo do arquivo, que lista
   os CPTs, e — se for catálogo ordenável — o array `$ordenaveis` em
   `cliconnect_admin_order_cpts()`.
2. **Taxonomia** (se houver) — em `cliconnect_register_taxonomies()`, mesmo arquivo.
3. **`inc/acf-fields-cpt.php`** — grupo `group_cli_{nome}`, chaves
   `field_{nome}_*`, localização por `post_type`, `hide_on_screen` igual aos irmãos.
   Use campo `message` para explicar o que vem da imagem destacada ou do título.
4. **Templates**, só se for público: `archive-cli_{nome}.php` e
   `single-cli_{nome}.php`.
5. **Card** — `template-parts/card-{nome}.php`, recebendo dados por `$args`.
6. **CSS** — se ganhou template próprio, `assets/css/{nome}.css` enfileirado por
   contexto em `inc/enqueue.php` (`is_post_type_archive`, `is_singular`).
7. **Seed** — `criar_{nome}()` em `inc/cli/seed.php`, chamado no bloco "Criando CPTs…".
8. **`flush_rewrite_rules`** — só necessário se o CPT for público:
   ```bash
   ./bin/wp rewrite flush
   ```

Regras do tema valem integralmente: prefixo `cliconnect_`, text-domain `cli`, guarda
`ABSPATH`, `?? ''` em todo `get_field()`, escape na saída, imagem por ID.

---

## Fase 3 — Popular e validar

```bash
./bin/wp cliconnect seed
./bin/wp post list --post_type=cli_{nome} --fields=ID,post_title,menu_order
./bin/wp rewrite flush          # só se público
```

Checklist:

- [ ] Menu do CPT aparece no painel, com labels em português corretos (singular,
      plural e gênero) — abra "Adicionar novo" e confira as frases.
- [ ] Grupo ACF aparece na tela de edição e **só** nela.
- [ ] Ordenação por `menu_order` funciona no admin e no front (`cliconnect_posts()`).
- [ ] Se público: `/{slug}/` abre o archive e um item abre o single, sem 404.
      404 quase sempre é `rewrite flush` esquecido.
- [ ] Onde o CPT é consumido, a seção renderiza — e some quando não há itens.
- [ ] `php -l` limpo nos arquivos tocados.
- [ ] Rode a skill `auditar-tema` no escopo dos arquivos novos.

Feche pedindo que o usuário cadastre um item pelo painel e confira o resultado — é o
teste real de que os campos e labels fazem sentido para quem edita.
