# Ambiente de desenvolvimento (Local)

O ambiente de dev roda no **Local (by Flywheel)**, que empacota PHP, MySQL, um servidor web
e o **MailPit** (captura de e-mail). Este doc cobre debug, plugins de auditoria e e-mail em
dev.

## Acesso ao shell e por que preferimos `bin/wp`

O Local expõe um **"site shell"** (menu do site no app → *Open site shell*) que injeta o
PHP e o `wp` corretos no ambiente — porque `wp`/`php` **não estão no PATH** externo.

Para automação (e para o agente) preferimos o wrapper **`bin/wp`**, que roda os mesmos
comandos **sem abrir o site shell interativo**. Detalhes em [`wp-cli.md`](wp-cli.md).

## Debug no `wp-config.php`

Flags de debug ficam no `wp-config.php` da instalação. Em desenvolvimento:

```php
define( 'WP_DEBUG', true );          // liga o modo de depuração
define( 'WP_DEBUG_LOG', true );      // grava erros em arquivo
define( 'WP_DEBUG_DISPLAY', false ); // não vaza erros na tela (usa o log)
```

**Logs:** `wp-content/debug.log`. O Local também expõe os logs pelo app (aba do site) e o
`error.log` do servidor.

> Vários módulos do tema usam `WP_DEBUG` como chave de ambiente: `inc/smtp.php` só liga o
> MailPit com debug ativo, e `inc/analytics.php` **nunca** imprime o GA/GTM com debug
> ativo. Produção deve rodar sempre com `WP_DEBUG` desligado.

## Plugins de auditoria (só-dev)

Ferramentas de inspeção que **não vão para produção**:

- **Query Monitor** — queries, hooks, tempos, HTTP e PHP notices por request.
- **Show Current Template** — mostra na admin bar qual arquivo da hierarquia de template
  está renderizando a página atual.

Desativar/remover antes do deploy.

## E-mail em dev com MailPit

O Local roda o **MailPit**, que **intercepta automaticamente todo e-mail** enviado pelo
site — `wp_mail()`, plugin de formulários etc. **Nenhum SMTP real é necessário em dev**;
nada sai para a internet. O módulo `inc/smtp.php` roteia o PHPMailer para o MailPit quando
`WP_DEBUG` está ligado (ajuste `CLICONNECT_SMTP_PORT` no `wp-config.php` se a porta do site
for outra).

- **Abrir o painel:** no app do Local, aba do site → **Open Mailpit**.
- **API HTTP para testes automatizados:**

```bash
curl -s http://localhost:8025/api/v1/messages                    # listar
curl -s "http://localhost:8025/api/v1/search?query=Contato"      # buscar
curl -s -X DELETE http://localhost:8025/api/v1/messages          # limpar
```

> A porta do MailPit é atribuída pelo Local; confira no app do site. Fluxo de teste:
> limpar a caixa → disparar a ação → consultar a API e assertar a mensagem.

**Produção:** SMTP real via WP Mail SMTP (ou similar) com as credenciais do cliente.
MailPit é exclusivo de dev.
