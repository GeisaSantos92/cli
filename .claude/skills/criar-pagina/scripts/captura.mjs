#!/usr/bin/env node
/**
 * captura.mjs — captura uma página do site local para revisão visual.
 *
 * Gera, por viewport: um PNG de página inteira + fatias verticais legíveis, e
 * relata erros de console, requisições falhas e scroll horizontal.
 *
 * Uso:
 *   node captura.mjs <url> <pasta-destino> [--viewports=1440,768,390] [--fatias=1600] [--espera=1500]
 *
 * Exemplo:
 *   node .claude/skills/criar-pagina/scripts/captura.mjs \
 *     "http://cli.local/plataforma/" /tmp/revisao-plataforma
 *
 * Requer Playwright — já disponível via cache do npx nesta máquina. O script
 * resolve o módulo sozinho; não instale nada no projeto (o tema não tem build).
 */

import { mkdir, readdir, writeFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import { homedir } from 'node:os';
import path from 'node:path';

/* ---------------------------------------------------------------- argumentos */

const [url, destino, ...flags] = process.argv.slice(2);

if (!url || !destino) {
	console.error('Uso: node captura.mjs <url> <pasta-destino> [--viewports=1440,768,390] [--fatias=1600] [--espera=1500]');
	process.exit(1);
}

const flag = (nome, padrao) => {
	const achado = flags.find((f) => f.startsWith(`--${nome}=`));
	return achado ? achado.split('=').slice(1).join('=') : padrao;
};

const viewports = flag('viewports', '1440,768,390').split(',').map((n) => parseInt(n, 10));
const alturaFatia = parseInt(flag('fatias', '1600'), 10);
const espera = parseInt(flag('espera', '1500'), 10);

/* ------------------------------------------------- resolução do Playwright */

async function candidatosPlaywright() {
	const candidatos = [];

	try {
		candidatos.push(await import('playwright'));
	} catch {
		/* sem playwright instalado no projeto — segue para o cache do npx */
	}

	// Cache do npx (~/.npm/_npx/<hash>/node_modules/playwright). Pode haver mais
	// de uma versão, e nem toda versão tem o browser baixado: testamos todas.
	const base = path.join(homedir(), '.npm', '_npx');

	if (existsSync(base)) {
		for (const dir of await readdir(base)) {
			const alvo = path.join(base, dir, 'node_modules', 'playwright', 'index.mjs');

			if (existsSync(alvo)) {
				try {
					candidatos.push(await import(alvo));
				} catch {
					/* ignora versão quebrada */
				}
			}
		}
	}

	return candidatos;
}

/**
 * Abre o primeiro Chromium que realmente inicia; se nenhum tiver browser
 * baixado, cai para o Google Chrome instalado na máquina.
 */
async function abrirNavegador() {
	const candidatos = await candidatosPlaywright();

	if (!candidatos.length) {
		console.error('Playwright não encontrado. Rode `npx playwright --version` uma vez para popular o cache do npx.');
		process.exit(1);
	}

	for (const { chromium } of candidatos) {
		try {
			return await chromium.launch();
		} catch {
			try {
				return await chromium.launch({ channel: 'chrome' });
			} catch {
				/* tenta o próximo candidato */
			}
		}
	}

	console.error('Nenhum Chromium disponível. Rode `npx playwright install chromium`.');
	process.exit(1);
}

/* ------------------------------------------------------------------ captura */

await mkdir(destino, { recursive: true });

const navegador = await abrirNavegador();
const relatorio = [];

for (const largura of viewports) {
	const contexto = await navegador.newContext({
		viewport: { width: largura, height: Math.round(largura * 0.66) },
		deviceScaleFactor: 1,
	});

	const pagina = await contexto.newPage();
	const erros = [];
	const falhas = [];

	pagina.on('console', (msg) => {
		if (msg.type() === 'error') {
			erros.push(msg.text());
		}
	});
	pagina.on('pageerror', (e) => erros.push(`JS: ${e.message}`));
	pagina.on('response', (r) => {
		if (r.status() >= 400) {
			falhas.push(`${r.status()} ${r.url()}`);
		}
	});

	const resposta = await pagina.goto(url, { waitUntil: 'networkidle', timeout: 45000 });

	// Rola até o fim para disparar lazy-load e animações presas a scroll.
	await pagina.evaluate(async () => {
		await new Promise((resolve) => {
			let y = 0;
			const passo = () => {
				y += window.innerHeight;
				window.scrollTo(0, y);

				if (y < document.body.scrollHeight) {
					setTimeout(passo, 100);
				} else {
					window.scrollTo(0, 0);
					setTimeout(resolve, 300);
				}
			};
			passo();
		});
	});

	await pagina.waitForTimeout(espera);

	const metricas = await pagina.evaluate(() => {
		const el = document.documentElement;
		const vazando = [...document.querySelectorAll('body *')]
			.filter((n) => n.getBoundingClientRect().right > el.clientWidth + 1)
			.slice(0, 5)
			.map((n) => `${n.tagName.toLowerCase()}.${(n.className || '').toString().split(' ')[0]}`);

		return {
			altura: document.body.scrollHeight,
			scrollHorizontal: el.scrollWidth > el.clientWidth,
			vazando,
			h1: [...document.querySelectorAll('h1')].map((n) => n.textContent.trim()),
			secoes: [...document.querySelectorAll('main > section, main > div > section')].map(
				(n) => n.className || '(sem classe)'
			),
			semAlt: document.querySelectorAll('img:not([alt])').length,
		};
	});

	const cheia = path.join(destino, `${largura}-completa.png`);
	await pagina.screenshot({ path: cheia, fullPage: true });

	// Fatias legíveis: página inteira em pedaços de `alturaFatia` px.
	const fatias = [];

	for (let i = 0, y = 0; y < metricas.altura; i++, y += alturaFatia) {
		const arquivo = path.join(destino, `${largura}-fatia-${String(i + 1).padStart(2, '0')}.png`);

		await pagina.screenshot({
			path: arquivo,
			clip: {
				x: 0,
				y,
				width: largura,
				height: Math.min(alturaFatia, metricas.altura - y),
			},
			fullPage: true,
		});

		fatias.push(arquivo);
	}

	relatorio.push({
		largura,
		status: resposta ? resposta.status() : null,
		...metricas,
		erros,
		falhas,
		arquivos: { completa: cheia, fatias },
	});

	await contexto.close();
}

await navegador.close();

const json = path.join(destino, 'relatorio.json');
await writeFile(json, JSON.stringify({ url, capturado: new Date().toISOString(), relatorio }, null, 2));

/* ------------------------------------------------------------------ resumo */

for (const r of relatorio) {
	console.log(`\n■ ${r.largura}px — HTTP ${r.status} — altura ${r.altura}px`);
	console.log(`  seções: ${r.secoes.length ? r.secoes.join(', ') : '(nenhuma <section> em <main>)'}`);
	console.log(`  h1: ${r.h1.length === 1 ? `"${r.h1[0]}"` : `${r.h1.length} encontrados ← revisar`}`);
	console.log(`  scroll horizontal: ${r.scrollHorizontal ? `SIM ← ${r.vazando.join(', ')}` : 'não'}`);
	console.log(`  img sem alt: ${r.semAlt}`);
	console.log(`  erros de console: ${r.erros.length ? r.erros.join(' | ') : 'nenhum'}`);
	console.log(`  requisições >=400: ${r.falhas.length ? r.falhas.join(' | ') : 'nenhuma'}`);
	console.log(`  imagens: ${r.arquivos.completa} (+${r.arquivos.fatias.length} fatias)`);
}

console.log(`\nRelatório: ${json}`);
