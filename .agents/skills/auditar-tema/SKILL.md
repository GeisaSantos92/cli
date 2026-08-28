---
name: auditar-tema
description: >
  Audita o tema CLI Connect contra as regras do projeto (escape de saída, coalescência
  nula, guarda ABSPATH, prefixo, tokens de CSS, zero texto fixo, enfileiramento por
  contexto, módulos carregados). Roda uma varredura por grep, faz a triagem separando
  violação real de exceção legítima, e entrega um relatório priorizado — corrigindo só
  o que o usuário aprovar. Use quando pedirem "auditar o tema", "revisar o código",
  "verificar se está no padrão", "checar boas práticas", "tem algo fora do padrão?",
  ou antes de um deploy/entrega.
---

# Auditar o tema

O tema tem ~20 regras rígidas e **nenhuma checagem automática** (sem phpcs, sem linter,
sem CI). Esta skill é o substituto: varre, tria e reporta.

O ponto crítico é a **triagem**. O script produz indícios; boa parte deles é exceção
legítima e documentada. Reportar tudo que o grep achou é pior que não auditar — vira
ruído e treina o usuário a ignorar o relatório.

---

## Fase 1 — Definir o escopo

Pergunte (ou infira do pedido) o que auditar:

| Escopo | Quando |
|---|---|
| **Tema inteiro** | pedido genérico, pré-deploy, primeira auditoria |
| **Arquivos alterados** | logo depois de implementar uma issue ou uma página |
| **Uma regra só** | "tem cor fora do token?" → `--regra=R07` |

```bash
bash .Codex/skills/auditar-tema/scripts/auditar.sh --formato=resumo   # panorama
bash .Codex/skills/auditar-tema/scripts/auditar.sh                    # lista completa
bash .Codex/skills/auditar-tema/scripts/auditar.sh --regra=R07        # uma regra
```

O script sai da raiz do tema sozinho e ignora `.Codex/`, `docs/`, `node_modules/`,
`vendor/` e `languages/`.

---

## Fase 2 — Triar (o trabalho de verdade)

Para **cada indício**, abra o arquivo na linha apontada e decida:

- **Violação** — vai para o relatório, com a correção proposta.
- **Exceção legítima** — não entra no relatório. Se o código ainda não explica por que é
  exceção, proponha adicionar o comentário (`// phpcs:ignore ...` ou uma linha de
  justificativa) — é isso que impede o indício de voltar na próxima auditoria.

Regra por regra, com as exceções já conhecidas deste tema:
[`references/regras.md`](references/regras.md).

**Nunca reporte um indício sem ter lido o código.** O grep não sabe se um `echo $x` é
XSS ou uma ternária de strings literais.

Vale também o que o grep não pega — passe o olho, quando o escopo permitir, em:

- `template-parts/` sem `return` cedo (seção que renderiza vazia);
- CSS de página enfileirado sem condição de contexto em `inc/enqueue.php`;
- campo ACF criado pelo painel em vez de código;
- lista que cresce modelada como campo numerado em vez de CPT;
- dado global em ACF em vez do Customizer.

---

## Fase 3 — Relatório

Agrupe por severidade, mais grave primeiro. Para cada item:

```
BLOQUEIO · R02 · assets/../arquivo.php:99
  O quê:    echo de variável sem escape.
  Por quê:  risco de XSS se o valor vier do banco.
  Correção: envolver em esc_attr().
```

Feche com um **resumo executivo**: quantos indícios, quantos viraram violação, quantos
foram descartados como exceção, e a recomendação (o que corrigir agora vs. o que pode
esperar).

Se a auditoria não achar nada, diga isso com clareza e mostre o que foi verificado —
"sem achados" só tem valor se o usuário souber a cobertura.

---

## Fase 4 — Corrigir (só com aprovação)

Apresente as correções propostas e pergunte, com `AskUserQuestion`:

| Pergunta | Opções |
|---|---|
| O que corrigir agora? | **Só BLOQUEIO** · **BLOQUEIO + PADRÃO** · **Tudo** · **Nada, só o relatório** |

Ao corrigir:

- Uma correção por vez, do mais grave para o menos.
- **Não refatore de carona.** Corrigir `#12b76a` → token é a correção; reorganizar o
  arquivo CSS não é.
- Correção que exige decisão de design (criar um token novo em `:root`, mudar uma cor)
  **não é correção automática** — proponha e espere o aval.
- Depois: `php -l` nos PHP tocados, recapture a página afetada
  (`.Codex/skills/criar-pagina/scripts/captura.mjs`) e **rode a auditoria de novo**
  para confirmar que o indício sumiu e nenhum novo apareceu.

---

## Estado conhecido (auditoria de referência)

Rodando no tema como está hoje: **33 indícios**, dos quais a maioria é exceção
legítima. Os que merecem atenção:

- **R07** — `#12b76a` e `rgba(18, 183, 106, .12)` em `front-page.css` (verde de status
  sem token; `--cor-positivo` é outro verde) e `#3551f2`/`#0041ba` escritos crus em
  gradientes, quando existem `--cor-primaria` e `--cor-primaria-escura`.
- **R10** — `inc/blocks.php` não é carregado por `functions.php`. Os 4 blocos,
  `blocks.css` e `page-templates/landing.php` estão no repositório e inertes. É
  intencional (o `SETUP.md` manda descomentar por projeto) — confirme com o usuário
  antes de "corrigir".
- **R12** — `template-parts/rodape-agencia.php:53` carrega um logo direto de
  `agenciar8.com.br`. Não é violação de regra do tema, mas é dependência externa numa
  página que precisa ser rápida.

Use isso como linha de base: um indício **novo** em relação a esta lista merece mais
atenção que os já conhecidos.
