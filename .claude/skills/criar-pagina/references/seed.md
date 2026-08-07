# Seed — popular a página (sempre)

Página criada sem conteúdo é entrega pela metade: o layout não se sustenta em campos
vazios e não dá para validar contra a referência. **Toda página nova ganha seed.**

O seeder é `inc/cli/seed.php` (classe `Cliconnect_Seed`, comando
`./bin/wp cliconnect seed`). Ele é **idempotente**: cada objeto criado guarda a meta
`_cliconnect_seed` com um slug estável, e rodar de novo atualiza em vez de duplicar.

---

## 1. Garantir a página em `criar_paginas()`

O array `$definicoes` mapeia `slug => Título`. Se a página ainda não estiver lá,
acrescente:

```php
		$definicoes = array(
			'home'            => 'Home',
			// ...
			'{slug}'          => '{Título}',
		);
```

`criar_paginas()` devolve `slug => ID`; é desse array que sai o ID usado no
preenchimento.

---

## 2. Assets

Duas pastas, dois destinos — decida item a item:

| Tipo | Destino | Como entra no código |
|---|---|---|
| **Ilustração fechada** (arte com texto embutido, diagrama, cartão pronto) | `assets/img/` (versionado) | `cliconnect_imagem_tema( 'page-{slug}-hero.png', [...] )` no template-part |
| **Conteúdo editável** (foto, logo, mockup, capa) | `assets/seed/` (ignorado pelo git) | importado por `importar_midia()` → ID via `$this->img( 'nome-do-arquivo' )` |

- Nomes em kebab-case com prefixo de contexto: `{slug}-hero.png`, `{slug}-card-1.png`.
- `importar_midia()` varre `assets/seed/*.{png,jpg,jpeg}`. Só isso — SVG não é importado
  como mídia; SVG de marca/ícone vai para `assets/img/`.
- O título/alt do anexo sai do nome do arquivo (`titulo_da_midia()`). Se o prefixo do
  contexto não deve aparecer no alt, acrescente-o à regex daquele método.

Quando o usuário respondeu na Fase 0 que **já exportou os assets manualmente**, não
baixe nada: liste a pasta informada, confira os nomes e mova/copie para o destino certo.

---

## 3. Método de preenchimento

Crie um método por página, no mesmo estilo de `preencher_home()`, na seção de páginas do
arquivo:

```php
	/**
	 * Preenche os campos ACF da página "{Título}".
	 *
	 * @param int $pagina_id ID da página.
	 * @return void
	 */
	protected function preencher_{slug}( $pagina_id ) {
		if ( ! $pagina_id ) {
			return;
		}

		$campos = array(
			// 1. Hero.
			'{slug}_hero_eyebrow' => 'Plataforma',
			'{slug}_hero_titulo'  => 'Título exatamente como está no layout',
			'{slug}_hero_texto'   => 'Parágrafo transcrito da referência.',
			'{slug}_hero_botao'   => $this->link( 'Agende uma demonstração', '/contato/' ),
			'{slug}_hero_imagem'  => $this->img( '{slug}-hero' ),

			// 2. ...
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $pagina_id );
		}

		WP_CLI::log( sprintf( '  {slug}: %d campos preenchidos.', count( $campos ) ) );
	}
```

E chame em `__invoke()`, logo depois de `preencher_home()`:

```php
		WP_CLI::log( '— Preenchendo a página {Título}…' );
		$this->preencher_{slug}( $paginas['{slug}'] );
```

Helpers já disponíveis na classe:

| Helper | Uso |
|---|---|
| `$this->upsert( $slug, $dados )` | cria/atualiza post por slug de seed |
| `$this->img( 'nome-do-arquivo' )` | ID do anexo importado de `assets/seed/` |
| `$this->link( 'Texto', '/destino/' )` | array de campo ACF Link (URL relativa vira `home_url()`) |
| `$this->definir_thumb( $post_id, 'arquivo' )` | define a imagem destacada |
| `$this->ids_por_titulo( $post_type, $titulos )` | resolve IDs de CPT por título |

Quebra de linha proposital no layout → use `"\n"` em string com aspas duplas (como
`camadas_titulo` na home) e trate no CSS/template.

---

## 4. CPT novo

Se o plano da Fase 2 previu um CPT, crie também o método de conteúdo, no padrão dos
existentes (`criar_integracoes()`, `criar_eventos()`…):

```php
	/**
	 * {Plural do CPT}.
	 *
	 * @return void
	 */
	protected function criar_{cpt}() {
		$itens = array(
			array( 'Título do item', 'arquivo-da-imagem', 'campo extra' ),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $titulo, $imagem, $extra ) = $item;

			$id = $this->upsert(
				'{cpt}:' . sanitize_title( $titulo ),
				array(
					'post_type'  => 'cli_{cpt}',
					'post_title' => $titulo,
					'menu_order' => $ordem,
				)
			);

			if ( ! $id ) {
				continue;
			}

			$this->definir_thumb( $id, $imagem );
			update_field( 'campo_extra', $extra, $id );
		}

		WP_CLI::log( sprintf( '  {cpt}: %d.', count( $itens ) ) );
	}
```

Chame-o no bloco "Criando CPTs…" do `__invoke()`. Depois de criar CPT novo,
`flush_rewrite_rules()` já é chamado no fim do seed — mas confirme os permalinks.

---

## 5. Executar

```bash
./bin/wp cliconnect seed          # incremental (padrão)
./bin/wp cliconnect seed --reset  # apaga tudo do seed e recria
```

- `--reset` remove **todo** post com a meta `_cliconnect_seed` e os menus do tema. Só
  rode se o usuário escolheu essa opção na Fase 0 — em ambiente com conteúdo real
  editado à mão, isso é destrutivo.
- Se `bin/wp.config.sh` não existir, o wrapper aborta com instrução. Repasse ao usuário
  (`cp bin/wp.config.example.sh bin/wp.config.sh` + ajuste de `PHP_BIN`, `DB_SOCKET`,
  `WP_PATH`) em vez de tentar rodar `wp` por outro caminho — ver `docs/wp-cli.md`.

Conferência rápida depois de rodar:

```bash
./bin/wp post list --post_type=page --fields=ID,post_title,post_name
./bin/wp eval 'var_export( get_field( "{slug}_hero_titulo", get_page_by_path("{slug}")->ID ) );'
./bin/wp eval 'echo home_url( "/{slug}/" );'
```

Se `get_field()` devolver vazio logo após criar a página pela primeira vez, é ordem de
registro: a página não existia quando o grupo ACF foi registrado. Rode o seed **de novo**
— na segunda passada o slug já resolve. (Por isso o grupo é sempre registrado, mesmo
sem localização válida: os nomes de campo continuam resolvendo em `update_field()`.)

---

## Conteúdo vindo de print

Quando a referência é só uma imagem, o texto do seed sai da **leitura da imagem**:

- transcreva **literalmente** — acentuação, maiúsculas, pontuação e quebras de linha;
- se algum trecho estiver ilegível ou cortado, **não invente**: marque na entrega e
  pergunte ao usuário;
- números de métricas, rótulos de botão e itens de lista entram como estão, mesmo que
  pareçam placeholder do layout.
