import Chart from 'chart.js/auto';

const chartColors = () => {
    const style = getComputedStyle(document.documentElement);
    return {
        primary: style.getPropertyValue('--primary').trim() || 'rgb(17, 255, 0)',
        accent: style.getPropertyValue('--accent').trim() || 'rgb(1, 233, 98)',
        text: style.getPropertyValue('--text').trim() || 'rgb(4, 3, 22)',
        border: style.getPropertyValue('--border').trim() || 'rgb(214, 214, 214)',
    };
};

export function initAdminCharts() {
    const salesCanvas = document.getElementById('chart-sales');
    const ordersCanvas = document.getElementById('chart-orders');

    if (!salesCanvas && !ordersCanvas) {
        return;
    }

    const colors = chartColors();

    Chart.defaults.color = colors.text;
    Chart.defaults.borderColor = colors.border;
    Chart.defaults.font.family = "'DM Sans', system-ui, sans-serif";

    if (salesCanvas) {
        const labels = JSON.parse(salesCanvas.dataset.labels || '[]');
        const values = JSON.parse(salesCanvas.dataset.values || '[]');

        new Chart(salesCanvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue',
                    data: values,
                    borderColor: colors.accent,
                    backgroundColor: 'rgba(1, 233, 98, 0.12)',
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
    }

    if (ordersCanvas) {
        const labels = JSON.parse(ordersCanvas.dataset.labels || '[]');
        const values = JSON.parse(ordersCanvas.dataset.values || '[]');

        new Chart(ordersCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Orders',
                    data: values,
                    backgroundColor: colors.primary,
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
    }
}
