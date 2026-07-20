(function () {
  const API_LEAD_CACHE = CONFIG.API_LEAD_CACHE;
  const MIN_INTERVAL_MS = 800;
  let lastSend = 0;

  function getSelectedPlanCode() {
    // 1) se o simulador já setou
    if (window.state?.planCode) return String(window.state.planCode).toLowerCase();
    if (window.planoSelecionado) return String(window.planoSelecionado).toLowerCase();

    // 2) lê do DOM (#planAccordion .plan.selected)
    const sel = document.querySelector('#planAccordion .plan.selected');
    if (sel?.dataset?.produto) return String(sel.dataset.produto).toLowerCase();

    // 3) fallback: qual painel está aberto?
    const opened = document.querySelector('#planAccordion .accordion-collapse.show');
    const plan = opened?.closest('.plan');
    if (plan?.dataset?.produto) return String(plan.dataset.produto).toLowerCase();
    if (plan?.classList.contains('plan-solo')) return 'solo';
    if (plan?.classList.contains('plan-trio')) return 'trio';

    // 4) default visual: duo costuma iniciar aberto
    return 'duo';
  }

  async function sendLeadCache() {
    const now = Date.now();
    if (now - lastSend < MIN_INTERVAL_MS) return;
    const uuid = localStorage.getItem('leadUuid');
    console.log('[LeadCache] firing… uuid=', uuid);
    if (!uuid) return;

    lastSend = now;

    const body = {
      token: uuid,
      produto_code: getSelectedPlanCode(),   // ← AQUI
      channel: "archa",
      origem_cliente: (typeof detectarOrigem === "function") ? detectarOrigem() : null,
      cliente: {
        nome: document.getElementById("nome")?.value.trim() || "",
        email: document.getElementById("email")?.value.trim() || "",
        telefone: document.getElementById("fone")?.value.trim() || ""
      },
      projeto: {
        metragem: +document.getElementById("area")?.value || 0,
        ambientes: +document.getElementById("qtd_amb")?.value || 0,
        prazo_dias: (typeof getPrazo === "function") ? getPrazo() : null,
        categoria: (typeof getCat === "function") ? getCat() : null,
        regiao_arquiteto: (typeof getReg === "function") ? getReg() : null,
        adicionais: (typeof getAdicionaisForLead === "function") ? getAdicionaisForLead() : [],
        tipo_projeto: (typeof getTipo === "function")
          ? getTipo()
          : (document.querySelector("#tipo_projeto")?.value?.trim() || null)
      }
    };

    try {
      const res = await fetch(API_LEAD_CACHE, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-API-TOKEN": API_TOKEN
        },
        body: JSON.stringify(body),
        keepalive: true
      });
      const js = await res.json().catch(() => ({}));
      console.log("[LeadCache] POST", res.status, js);
    } catch (err) {
      console.warn("[LeadCache] erro ao enviar:", err);
    }
  }

  window.sendLeadCache = sendLeadCache;
  console.log('[LeadCache] loaded + ready', location.href);
})();
