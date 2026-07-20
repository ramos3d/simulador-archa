/**
 * Wrapper para eventos GA4 / GTM.
 * Falha silenciosamente se o ArchaAnalytics não estiver disponível.
 */
const Analytics = {
    _call(method, ...args) {
        if (window.ArchaAnalytics && typeof window.ArchaAnalytics[method] === 'function') {
            try { window.ArchaAnalytics[method](...args); } catch { }
        }
    },

    trackCheckoutView:          (url)         => Analytics._call('trackCheckoutView', url),
    trackCheckoutAddress:       (...a)        => Analytics._call('trackCheckoutAddress', ...a),
    trackCheckoutPaymentMethod: (method, n)   => Analytics._call('trackCheckoutPaymentMethod', method, n),
    trackCheckoutCupom:         (code, valid) => Analytics._call('trackCheckoutCupom', code, valid),
    trackPaymentIntent:         (...a)        => Analytics._call('trackPaymentIntent', ...a),
    trackPaymentStatus:         (...a)        => Analytics._call('trackPaymentStatus', ...a),
};

export default Analytics;
