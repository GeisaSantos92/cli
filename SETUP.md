# SETUP.md — Ficha e processo de setup de um projeto novo

Este starter está **em branco**: sem marca, sem conteúdo, só infraestrutura e códigos de
exemplo. Para iniciar um projeto real, preencha a **Ficha do projeto** abaixo e siga o
**Processo de setup**. A ficha existe para concentrar, num lugar só, tudo que o tema
precisa saber no dia zero — um agente de IA consegue executar o setup inteiro a partir
dela.

---

## 1. Ficha do projeto (preencher antes de tudo)

### Identificação

| Campo | Valor | Usado em |
| --- | --- | --- |
| Nome do tema (ex.: `Acme`) | | `style.css` (Theme Name), comentários |
| Slug/pasta (ex.: `acme`) | | nome da pasta, text-domain |
| Prefixo PHP (ex.: `acme`) | | funções `acme_*`, handles `acme-*`, constantes `ACME_*`, `@package Acme` |
| Descrição curta | | `style.css` (Description) |
| Autor / Author URI | | `style.css` |
| URL de produção | | `style.css` (Theme URI) |

### Identidade visual

| Campo | Valor | Usado em |
| --- | --- | --- |
| Logo claro (fundos escuros) — arquivo | | Customizer → Logo Claro (header) |
| Logo escuro (fundos claros) — arquivo | | Customizer → Logo Escuro (footer, login) |
| Cor primária (hex) | | `--cor-primaria` em `assets/css/theme.css` + `--login-primaria` em `inc/login.php` |
| Cor primária escura (hex) | | `--cor-primaria-escura` + `--login-primaria-escura` |
| Fonte(s) | | `--fonte-base` (self-host os arquivos em `assets/fonts/`) |
| Screenshot 1200×900 | | `screenshot.png` na raiz |

### Estrutura e conteúdo

| Campo | Valor |
| --- | --- |
| Páginas iniciais (home, contato, ...) | |
| A home é `front-page.php` mapeada com ACF? (sim/não) | |
| Blog/notícias? (usa `post` nativo ou CPT?) | |
| CPTs previstos (nome, campos, tem archive?) | |
| Dados globais além do padrão (telefone, e-mails, endereços...) | |
| Menus (o starter registra `principal`, `rodape`, `rodape_legal`) | |

### Funcionalidades

| Funcionalidade | Sim/Não | Observação |
| --- | --- | --- |
| Landing pages editáveis com blocos | | descomentar `cliconnect_require( '/inc/blocks.php' )` — ver docs/blocks.md |
| Formulários (qual? destino dos e-mails?) | | plugin único — ver docs/best-practices.md |
| Multilíngue (Polylang) | | |
| Busca no site | | `search.php` já incluso |
| Google Analytics / GTM | | snippet vai no Customizer; injeção já pronta em `inc/analytics.php` |
| SEO (Rank Math ou similar) | | |
| Redes sociais do cliente | | manter só as usadas em `cliconnect_social_networks()` |

### Ambiente

| Campo | Valor |
| --- | --- |
| Nome do site no LocalWP | |
| Porta SMTP do MailPit (se ≠ 10006) | | `CLICONNECT_SMTP_PORT` no `wp-config.php` |
| Repositório git (URL) | |

---

## 2. Processo de setup

### Passo 1 — Copiar e renomear

```bash
cp -R cli "/caminho/wp-content/themes/{slug}"
cd "/caminho/wp-content/themes/{slug}"
./bin/rename.sh --slug=acme --prefix=acme --name="Acme"
```

O `bin/rename.sh` faz a busca/substituição de todos os tokens do starter
(`cliconnect_` → `acme_`, `cli` → `acme`, `CLICONNECT_` → `ACME_`,
`Cliconnect` → `Acme`, `CLI Connect` → `Acme`). Sem o script, faça manualmente
nessa mesma ordem.

### Passo 2 — Metadados e identidade

1. Revise `style.css`: Description, Author, Theme URI (o rename já troca nome e
   text-domain).
2. Troque a paleta: custom properties no topo de `assets/css/theme.css` e as variáveis
   `--login-*` em `inc/login.php`.
3. Adicione `screenshot.png` (1200×900).
4. Revise `cliconnect_social_networks()` em `inc/customizer.php` (agora com o prefixo novo)
   — deixe só as redes do cliente.

### Passo 3 — Versionamento

```bash
git init && git add -A && git commit -m "chore: scaffold do tema a partir do cli"
```

### Passo 4 — Ambiente e ativação

1. Configure o wrapper WP-CLI: `cp bin/wp.config.example.sh bin/wp.config.sh` e ajuste
   os caminhos ([docs/wp-cli.md](docs/wp-cli.md)).
2. `wp-config.php` de dev: `WP_DEBUG` + `WP_DEBUG_LOG` ligados
   ([docs/local-env.md](docs/local-env.md)).
3. Ative o tema: `./bin/wp theme activate {slug}`.
4. Instale os plugins da ficha: `./bin/wp plugin install advanced-custom-fields --activate` etc.

### Passo 5 — Conteúdo inicial (dados da ficha)

1. Customizer → Opções do Tema: envie os **logos**, preencha **redes sociais**,
   **contatos** e o snippet de **GA/GTM**.
2. Crie os menus e atribua às locations `principal`, `rodape`, `rodape_legal`.
3. Crie as páginas iniciais; defina a home em Configurações → Leitura, se houver.
4. Para cada CPT da ficha: duplique `inc/cpt-exemplo.php` e `inc/acf-fields-exemplo.php`,
   renomeie, descomente os requires em `functions.php` e rode `./bin/wp rewrite flush`.
5. Se a ficha pedir landing pages com blocos: descomente o require de `inc/blocks.php`,
   crie uma página com o template "Landing Page (canvas)" e ajuste/expanda os blocos de
   `blocks/` ([docs/blocks.md](docs/blocks.md)).

### Passo 6 — Verificação final

- [ ] Home, uma página interna, busca e uma URL inexistente (404) renderizam sem erro.
- [ ] `wp-content/debug.log` sem warnings do tema.
- [ ] Menu mobile abre/fecha (botão hambúrguer, Esc fecha).
- [ ] Logos aparecem no header, footer e tela de login.
- [ ] Nenhuma ocorrência restante dos tokens do starter:
  `grep -rn "starter\|cli\|Started" --include="*.php" --include="*.css" .`
- [ ] E-mail de teste chega no MailPit (`./bin/wp eval 'wp_mail("a@b.c","t","t");'`).

---

> Regras de código e arquitetura durante o desenvolvimento: [CLAUDE.md](CLAUDE.md) e
> [docs/](docs/).
