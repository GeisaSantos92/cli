# WP-CLI — por que e como usar

O WP-CLI é a via padrão para operar o site sem clicar no painel: instalar plugins, criar
menus/termos/opções, importar mídia, auditar dados e rodar seeders custom. É repetível,
versionável e roda direto do terminal.

## O problema: `wp`/`php` fora do PATH

No **Local (by Flywheel)**, `wp` e `php` **não estão no PATH** do terminal externo. O Local
só os expõe dentro do **"site shell"** (menu do site → *Open site shell*). Abrir esse shell
interativo a cada comando quebra a automação e o fluxo do agente.

## A solução: wrapper `bin/wp`

O wrapper **`bin/wp`** invoca o **PHP do Local** + o `wp-cli.phar` (baixado sob demanda),
apontando `--path` para a instalação e o **socket correto do MySQL** — assim os comandos
rodam do terminal externo, sem abrir o site shell.

### Setup (uma vez por máquina)

```bash
cp bin/wp.config.example.sh bin/wp.config.sh
# Ajuste PHP_BIN, DB_SOCKET e WP_PATH no wp.config.sh:
ls "$HOME/Library/Application Support/Local/lightning-services/"        # versão do PHP
ls "$HOME/Library/Application Support/Local/run/"*/mysql/mysqld.sock    # socket do MySQL
```

A config é **sensível à máquina** (versão do PHP e `<id>` do socket mudam por máquina/site)
e por isso fica em `bin/wp.config.sh`, **ignorada pelo git** — só o exemplo é versionado.

## Receitas práticas

### Plugins

```bash
bin/wp plugin install advanced-custom-fields query-monitor --activate
bin/wp plugin install /caminho/plugin-pago.zip --activate   # plugins fora do wp.org
bin/wp plugin list --status=active
```

### Menus, termos e opções

```bash
bin/wp menu create "Principal"
bin/wp menu location assign principal principal

bin/wp term create category "Notícias" --slug=noticias
bin/wp option update blogdescription "Descrição do site"
```

### Mídia e conteúdo

```bash
bin/wp media import ./img/hero.jpg --title="Hero" --porcelain   # devolve o ID do anexo
bin/wp post list --post_type=page --fields=ID,post_title,post_parent --format=table
```

### Inspeção rápida com `wp eval`

```bash
bin/wp eval 'echo home_url();'
bin/wp eval 'var_export( get_option("template") );'
```

### Banco e rewrite rules

```bash
bin/wp db check
bin/wp rewrite flush          # após mudar CPTs/permalinks
```

### Exportar / importar

```bash
bin/wp export --dir=./_dump                 # WXR do conteúdo
bin/wp import ./_dump/*.xml --authors=create
bin/wp db export backup.sql                 # dump SQL completo
```

## Comandos custom do tema

Registre seeders/utilitários do projeto em `inc/cli/bootstrap.php` (carregado só no
contexto CLI). Casos de uso típicos: seed de páginas/menus a partir de um template
estático (parse de HTML, sideload de imagens, upsert idempotente por meta) e rotinas de
setup repetíveis.

## Boas práticas

- **Auditoria via CLI:** prefira `wp post list`, `wp eval`, `wp db query` para inspecionar
  dados em vez de navegar no painel — é mais rápido e reproduzível.
- **Idempotência:** comandos de seed/setup devem poder rodar mais de uma vez sem duplicar
  (upsert por meta, checagem de existência). Nunca dependa de "rodar só uma vez".
- **Cuidado com produção:** operações destrutivas (`db reset`, `db import`,
  `search-replace`, `plugin delete`) são irreversíveis. Faça `wp db export` antes. Em
  produção, deploy é migração atômica (WP Migrate), **não** search-replace bruto em `.sql`.
- **`--dry-run` / `--porcelain`:** use quando disponível para prever efeito e capturar IDs
  em scripts.
