---
name: traduzir-polylang
description: >
  Traduz o site CLI Connect com o Polylang (pt/en): habilita CPTs e taxonomias para
  tradução, vincula páginas/posts/CPTs traduzidos, sincroniza os menus nas locations por
  idioma (o gotcha que deixa o menu vazio no front), extrai e traduz as strings do tema
  (.pot/.po/.mo) e as strings do Customizer, e audita o que ainda falta. Use quando
  pedirem "traduzir o site", "versão em inglês", "configurar o Polylang", "o menu em
  inglês está vazio", "falta tradução", "adicionar um idioma".
---

# Traduzir com Polylang

O site é bilíngue (pt/en). Hoje o tema tem **três** pontos de integração prontos:
`cliconnect_seletor_idiomas()` (header), `sincronizar_polylang()` (seed) e o registro do
text-domain `cli`. **O resto não está feito** — `languages/` está vazio e não há
tradução de conteúdo.

Tradução aqui tem **quatro camadas independentes**. Confundi-las é o que faz o trabalho
parecer "quase pronto" e nunca terminar:

| Camada | O que é | Onde vive |
|---|---|---|
| **1. Strings do tema** | `esc_html_e( 'Ler mais', 'cli' )` | `languages/*.po` + `.mo` |
| **2. Strings de opção** | textos digitados no Customizer | `pll_register_string()` |
| **3. Conteúdo** | páginas, posts, CPTs, campos ACF | posts distintos, vinculados |
| **4. Estrutura** | menus, locations, templates | opção `polylang` + filtros |

Detalhes de cada uma: [`references/camadas.md`](references/camadas.md).

---

## Fase 0 — Diagnóstico (sempre primeiro)

```bash
./bin/wp plugin list --status=active --format=table
./bin/wp eval 'var_export( function_exists("pll_languages_list") ? pll_languages_list() : "Polylang inativo" );'
./bin/wp eval '$o = get_option("polylang"); var_export( array( "post_types" => $o["post_types"] ?? [], "taxonomies" => $o["taxonomies"] ?? [], "nav_menus" => array_keys( $o["nav_menus"][ get_stylesheet() ] ?? [] ) ) );'
ls -la languages/
```

Isso responde: o plugin está ativo? quais idiomas existem? **quais CPTs estão
habilitados para tradução?** os menus estão sincronizados? há arquivos de tradução?

Rode também a auditoria de tradução:

```bash
bash .Codex/skills/traduzir-polylang/scripts/auditar-traducao.sh
```

Ela lista, por idioma: páginas/posts/CPTs sem tradução, menus sem location por idioma e
strings do tema sem `.mo`.

---

## Fase 1 — Apresentar o diagnóstico e perguntar (PORTÃO)

Traduzir é caro e nem tudo precisa ser traduzido. Apresente o levantamento e pergunte,
com `AskUserQuestion`:

| # | Pergunta | Opções |
|---|---|---|
| 1 | O que traduzir agora? | **Só a estrutura** (menus, locations, strings do tema) · **Estrutura + páginas principais** · **Tudo, inclusive CPTs** |
| 2 | Quais CPTs devem ser traduzíveis? | multi-seleção entre os 7 — **catálogo de logo geralmente não precisa** (`cli_cliente`, `cli_integracao`, `cli_selo`) |
| 3 | Quem escreve o texto em inglês? | **O usuário fornece** · **Você traduz e ele revisa** (marcar como rascunho) |

Sobre a 2: habilitar um CPT no Polylang **muda o comportamento das queries** — a partir
daí `cliconnect_posts()` passa a devolver só os itens do idioma atual, e um catálogo sem
tradução some do site em inglês. Explique isso antes; é o erro mais caro de desfazer.

Sobre a 3: **nunca publique tradução automática sem aviso.** Se você traduzir, diga
explicitamente que é rascunho a revisar.

---

## Fase 2 — Executar, camada por camada

Sempre nesta ordem — cada camada depende da anterior.
Comandos e código: [`references/camadas.md`](references/camadas.md).

1. **Habilitar tipos** — CPTs e taxonomias escolhidos entram em
   `polylang['post_types']` / `['taxonomies']`. Sem isso, o Polylang **nem oferece** o
   campo de tradução.
2. **Estrutura** — criar/vincular as páginas em inglês, sincronizar os menus
   (`polylang['nav_menus'][tema][location][idioma]`) e conferir o
   `template_include` das traduções.
3. **Conteúdo** — vincular cada par pt/en com `pll_set_post_language()` +
   `pll_save_post_translations()`, preenchendo os campos ACF **de cada post**
   (tradução é outro post: tem os próprios valores de campo).
4. **Strings do tema** — gerar o `.pot`, traduzir o `.po`, compilar o `.mo`.
5. **Strings de opção** — registrar os textos do Customizer com `pll_register_string()`
   e lê-los com `pll__()`.

Tudo que criar conteúdo deve entrar no **seed** (`inc/cli/seed.php`), idempotente, como
todo o resto — não faça pelo painel o que precisa sobreviver a um `--reset`.

---

## Fase 3 — Validar

```bash
./bin/wp eval 'echo home_url("/en/");'
node .Codex/skills/criar-pagina/scripts/captura.mjs "http://<site>.local/en/" /tmp/en
```

Checklist:

- [ ] `/en/` abre e **o menu aparece** (menu vazio = `nav_menus` não sincronizado).
- [ ] O seletor de idiomas mostra os dois idiomas e navega entre as versões
      correspondentes, não sempre para a home.
- [ ] A página traduzida usa o **template certo** — se cair no `page.php` genérico,
      falta o filtro `template_include`.
- [ ] Campos ACF preenchidos na versão EN (tradução é outro post).
- [ ] Strings de interface em inglês (se o `.mo` foi compilado).
- [ ] Seções que dependem de CPT não sumiram em EN.
- [ ] Rode `auditar-traducao.sh` de novo: o que restou é o que falta.

Feche listando **o que ficou sem tradução e por quê**, e peça a revisão do texto em
inglês pelo usuário — revisão de idioma é dele, não sua.
