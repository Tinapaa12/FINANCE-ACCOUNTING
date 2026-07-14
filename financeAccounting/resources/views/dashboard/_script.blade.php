<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const chartData = @json($chartData);

    let cashFlowChartInstance = null;
    let revenueExpensesChartInstance = null;

    function dashboard() {
        return {
            chartsInitialized: false,

            initCharts() {
                if (this.chartsInitialized) return;
                this.$nextTick(() => {
                    const cashCanvas = document.getElementById('cashFlowChart');
                    const revCanvas = document.getElementById('revenueExpensesChart');
                    if (!cashCanvas || !revCanvas) return;

                    if (cashFlowChartInstance) cashFlowChartInstance.destroy();
                    if (revenueExpensesChartInstance) revenueExpensesChartInstance.destroy();

                    const labels = chartData.cash_flow.map(d => d.month);

                    const yTickValues = [10000, 50000, 100000, 500000];
                    function filterYTicks(axis) {
                        axis.ticks = axis.ticks.filter(t => yTickValues.includes(t.value));
                    }

                    cashFlowChartInstance = new Chart(cashCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'Cash In', data: chartData.cash_flow.map(d => d.cash_in), backgroundColor: '#22c55e', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { label: 'Cash Out', data: chartData.cash_flow.map(d => d.cash_out), backgroundColor: '#ef4444', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { type: 'line', label: 'Net Cash Flow', data: chartData.cash_flow.map(d => d.net), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, pointRadius: 3, pointBackgroundColor: '#3b82f6', borderWidth: 2, fill: true }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 800, easing: 'easeOutQuart' },
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, callback: v => v / 1000 + 'k' }, afterBuildTicks: filterYTicks }
                            }
                        }
                    });

                    revenueExpensesChartInstance = new Chart(revCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: chartData.revenue_expenses.map(d => d.month),
                            datasets: [
                                { label: 'Revenue', data: chartData.revenue_expenses.map(d => d.revenue), backgroundColor: '#3b82f6', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { label: 'Expenses', data: chartData.revenue_expenses.map(d => d.expenses), backgroundColor: '#ef4444', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 800, easing: 'easeOutQuart' },
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, callback: v => v / 1000 + 'k' }, afterBuildTicks: filterYTicks }
                            }
                        }
                    });

                    this.chartsInitialized = true;
                });
            }
        }
    }
</script>
