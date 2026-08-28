# Triagem

Cada achado medido cai em uma de quatro caixas. Só a primeira vira tarefa de correção;
as outras três existem para **não** virar.

---

## 1. Divergência real

O código não faz o que o layout pede, e ninguém decidiu isso.

Sinais: quantidade diferente de itens, ordem trocada, elemento do frame que não existe no
DOM, largura declarada diferente da do frame, asset com conteúdo errado.

Vai para o relatório com a **causa**, não só o sintoma. "11 selos em vez de 10" é o
sintoma; "`cliconnect_posts('cli_selo')` lista todos sem filtro, então qualquer selo novo
volta a quebrar a grade" é a causa — e é o que decide o tamanho da correção.

## 2. Decisão deliberada

O código diverge porque alguém escolheu divergir. **Procure antes de acusar:**

```bash
git log --oneline -- template-parts/home/{secao}.php   # commit que mexeu
git log -S"'depoimento'" --oneline -- front-page.php   # commit que removeu a linha
```

A mensagem de commit costuma dizer o motivo e fechar uma issue (`closes #33`). Nesse caso
o item entra no relatório como **confirmar**, com o commit citado — nunca como bug. A
pergunta é "ainda vale?", não "quem quebrou?".

Vale o mesmo para conteúdo vindo do banco: campo com um item onde o layout mostra dois é
lacuna de dado, não de código.

## 3. Erro do próprio layout

O Figma está errado e o site está certo. Acontece com typo e acentuação.

Nesta base já apareceram: "integração já prontas para uso" (o site escreve "integrações")
e "Industria" sem acento (o site escreve "Indústria"). Ambos ficam **como estão no site**.

Vai para o relatório como "manter o site", para que ninguém "corrija" na direção errada
na próxima revisão.

## 4. Fora de critério

Não é fidelidade de composição:

- altura total da página, altura de seção, respiro entre blocos;
- diferença de poucos pixels em espaçamento interno;
- conteúdo de seed que é obviamente exemplo (título de post repetido no Figma);
- mockup em placeholder no Figma e finalizado no site.

Descartado. Se for um caso limítrofe que o usuário pode querer conhecer, uma linha no
final do relatório basta — não uma issue.

---

## Ordem de severidade no relatório

1. **Estrutural** — seção faltando ou sobrando, ordem trocada, quantidade errada de itens.
2. **Conteúdo** — texto, valor de campo, asset com a arte errada.
3. **Layout** — largura, grade, proporção, alinhamento.
4. **Confirmar** — decisões deliberadas e erros do layout.

Dentro de cada faixa, o que afeta mais páginas primeiro: rodapé e header valem mais que
uma seção isolada da home.
