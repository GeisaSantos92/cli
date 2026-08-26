---
name: create-theme-page
description: Instruções e melhores práticas para a criação de novas páginas mapeadas com ACF no tema. Use esta skill quando o usuário solicitar a criação de uma nova página (ex: sobre, contato, serviços).
---

# Criação de Páginas no Tema

Este tema segue uma arquitetura baseada em páginas mapeadas com campos do ACF (Advanced Custom Fields), sem uso do Gutenberg/FSE (blocos estruturais).
Quando o usuário solicitar a criação de uma nova página no tema, siga rigorosamente os passos abaixo conforme a documentação em `docs/project-structure.md`:

## Passo 1: O arquivo principal da página
Crie um arquivo chamado `page-{slug}.php` na raiz do tema.
O `{slug}` deve corresponder ao slug real que a página terá no WordPress.

Exemplo (`page-sobre.php`):
```php
<?php
/**
 * Template Name: Sobre
 */
get_header(); ?>

<main id="primary" class="site-main">
    <?php
    // Carregue as seções da página
    get_template_part('template-parts/sobre', 'hero');
    // get_template_part('template-parts/sobre', 'outra-secao');
    ?>
</main>

<?php
get_footer();
```

## Passo 2: Os componentes (Template Parts)
Para cada seção da página, crie arquivos dentro do diretório `template-parts/` seguindo a convenção de nomenclatura `{slug}-{secao}.php`.

Exemplo (`template-parts/sobre-hero.php`):
```php
<?php
// Recupere os dados do ACF
$hero_titulo = get_field('sobre_hero_titulo') ?? '';
$hero_texto  = get_field('sobre_hero_texto') ?? '';
?>
<section class="sobre-hero">
    <div class="container">
        <?php if ($hero_titulo) : ?>
            <h1><?php echo esc_html($hero_titulo); ?></h1>
        <?php endif; ?>
        
        <?php if ($hero_texto) : ?>
            <div class="sobre-hero__texto">
                <?php echo wp_kses_post($hero_texto); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
```
*Lembre-se das Regras:* 
- Todo dado impresso deve possuir fallback seguro com coalescência nula (`?? ''`).
- Todo dado impresso deve usar **escape** na saída (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).

## Passo 3: O grupo de campos locais do ACF
Para que a página seja editável no painel, crie um arquivo no diretório `inc/` com o padrão `acf-fields-{slug}.php`.
Este arquivo deve registrar o Field Group via código (`acf_add_local_field_group`) e **nunca** no painel administrativo do site.

Se você não souber de cabeça como escrever um array do ACF, copie o modelo existente em `inc/acf-fields-exemplo.php` ou de outras páginas já existentes (como `inc/acf-fields-home.php`).

Importante: Não se esqueça de ir no arquivo `functions.php` e adicionar um `require` para este novo arquivo recém criado (`require_once get_template_directory() . '/inc/acf-fields-{slug}.php';` ou o método padrão usado pelo arquivo).

## Passo 4: Enfileiramento de Estilos (Opcional)
Se a página tiver uma folha de estilos específica, crie o arquivo em `assets/css/{slug}.css` e enfileire-o no arquivo `inc/enqueue.php`, garantindo que ele só carregue quando `is_page('{slug}')` for verdadeiro.
