/**
 * Wrapper HTTP centralizado.
 * Lê api-base e api-token das metas do HTML (emitidas pelo PHP).
 */
const Api = (() => {
    const meta  = (name) => document.querySelector(`meta[name="${name}"]`)?.content ?? '';
    const BASE  = meta('api-base');
    const TOKEN = meta('api-token');
    const BRIDGE = './bridge.php';

    function endpoint(path) {
        // Só chama direto a API quando o token está exposto no HTML.
        // Caso contrário roteia pelo bridge.php (que injeta o token server-side).
        if (BASE && TOKEN) return BASE.replace(/\/+$/, '') + '/' + path.replace(/^\/+/, '');
        const op = path.replace(/^\/+/, '').replace(/\//g, '_');
        return `${BRIDGE}?op=${op}`;
    }

    function headers() {
        const h = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        if (BASE && TOKEN) h['X-API-TOKEN'] = TOKEN;
        return h;
    }

    async function post(path, body) {
        const resp = await fetch(endpoint(path), {
            method: 'POST',
            headers: headers(),
            body: JSON.stringify(body),
        });
        const json = await resp.json().catch(() => ({}));
        return { ok: resp.ok, status: resp.status, data: json };
    }

    async function get(path) {
        const resp = await fetch(endpoint(path), { headers: headers() });
        const json = await resp.json().catch(() => ({}));
        return { ok: resp.ok, status: resp.status, data: json };
    }

    return { post, get, endpoint };
})();

export default Api;
