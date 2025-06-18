import React, { useState, useEffect } from 'react';
import { Calculator as CalcIcon, TrendingUp, DollarSign, Calendar, Percent } from 'lucide-react';

const Calculator = () => {
  const [investment, setInvestment] = useState(1000);
  const [dailyRate, setDailyRate] = useState(2.0);
  const [duration, setDuration] = useState(30);
  const [compounding, setCompounding] = useState(false);
  const [results, setResults] = useState({
    dailyProfit: 0,
    totalProfit: 0,
    totalReturn: 0,
    roi: 0,
  });

  const investmentPlans = [
    { name: 'Starter Plan', rate: 1.5, duration: 30, min: 100, max: 999 },
    { name: 'Growth Plan', rate: 2.0, duration: 45, min: 1000, max: 4999 },
    { name: 'Premium Plan', rate: 2.5, duration: 60, min: 5000, max: 19999 },
    { name: 'Elite Plan', rate: 3.0, duration: 90, min: 20000, max: 100000 },
  ];

  useEffect(() => {
    calculateReturns();
  }, [investment, dailyRate, duration, compounding]);

  const calculateReturns = () => {
    const principal = parseFloat(investment.toString()) || 0;
    const rate = parseFloat(dailyRate.toString()) / 100 || 0;
    const days = parseInt(duration.toString()) || 0;

    let totalAmount = principal;
    let totalProfit = 0;

    if (compounding) {
      // Compound interest calculation
      totalAmount = principal * Math.pow(1 + rate, days);
      totalProfit = totalAmount - principal;
    } else {
      // Simple interest calculation
      const dailyProfit = principal * rate;
      totalProfit = dailyProfit * days;
      totalAmount = principal + totalProfit;
    }

    const roi = principal > 0 ? (totalProfit / principal) * 100 : 0;

    setResults({
      dailyProfit: principal * rate,
      totalProfit,
      totalReturn: totalAmount,
      roi,
    });
  };

  const selectPlan = (plan: typeof investmentPlans[0]) => {
    setDailyRate(plan.rate);
    setDuration(plan.duration);
    if (investment < plan.min) {
      setInvestment(plan.min);
    } else if (investment > plan.max) {
      setInvestment(plan.max);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center space-y-4 mb-12">
          <div className="flex justify-center">
            <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center">
              <CalcIcon className="h-8 w-8 text-white" />
            </div>
          </div>
          <h1 className="text-4xl font-bold text-gray-900">Profit Calculator</h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Calculate your potential returns before investing. Make informed decisions with our 
            advanced profit calculator that shows daily profits, total returns, and ROI.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Calculator Form */}
          <div className="lg:col-span-2 space-y-8">
            {/* Quick Plan Selection */}
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h3 className="text-2xl font-semibold text-gray-900 mb-6">Quick Plan Selection</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {investmentPlans.map((plan, index) => (
                  <button
                    key={index}
                    onClick={() => selectPlan(plan)}
                    className="p-4 border-2 border-gray-200 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all duration-200 text-left"
                  >
                    <div className="font-semibold text-gray-900">{plan.name}</div>
                    <div className="text-yellow-600 font-bold">{plan.rate}% Daily</div>
                    <div className="text-sm text-gray-600">{plan.duration} days</div>
                    <div className="text-sm text-gray-500">
                      ${plan.min.toLocaleString()} - ${plan.max.toLocaleString()}
                    </div>
                  </button>
                ))}
              </div>
            </div>

            {/* Manual Calculator */}
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h3 className="text-2xl font-semibold text-gray-900 mb-6">Custom Calculation</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-gray-700">
                    Investment Amount ($)
                  </label>
                  <div className="relative">
                    <DollarSign className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input
                      type="number"
                      value={investment}
                      onChange={(e) => setInvestment(Number(e.target.value))}
                      className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                      placeholder="1000"
                      min="100"
                      max="100000"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label className="block text-sm font-medium text-gray-700">
                    Daily Return Rate (%)
                  </label>
                  <div className="relative">
                    <Percent className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input
                      type="number"
                      value={dailyRate}
                      onChange={(e) => setDailyRate(Number(e.target.value))}
                      className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                      placeholder="2.0"
                      min="0.1"
                      max="10"
                      step="0.1"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label className="block text-sm font-medium text-gray-700">
                    Investment Duration (Days)
                  </label>
                  <div className="relative">
                    <Calendar className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input
                      type="number"
                      value={duration}
                      onChange={(e) => setDuration(Number(e.target.value))}
                      className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                      placeholder="30"
                      min="1"
                      max="365"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label className="block text-sm font-medium text-gray-700">
                    Compounding
                  </label>
                  <div className="flex items-center space-x-3 pt-3">
                    <input
                      type="checkbox"
                      id="compounding"
                      checked={compounding}
                      onChange={(e) => setCompounding(e.target.checked)}
                      className="h-5 w-5 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded"
                    />
                    <label htmlFor="compounding" className="text-gray-700">
                      Enable compound interest
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Results Panel */}
          <div className="space-y-6">
            <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-8 text-white">
              <div className="flex items-center space-x-3 mb-6">
                <TrendingUp className="h-8 w-8" />
                <h3 className="text-2xl font-semibold">Calculation Results</h3>
              </div>
              
              <div className="space-y-6">
                <div>
                  <div className="text-sm opacity-90">Daily Profit</div>
                  <div className="text-3xl font-bold">
                    ${results.dailyProfit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </div>
                </div>

                <div>
                  <div className="text-sm opacity-90">Total Profit</div>
                  <div className="text-3xl font-bold">
                    ${results.totalProfit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </div>
                </div>

                <div>
                  <div className="text-sm opacity-90">Total Return</div>
                  <div className="text-3xl font-bold">
                    ${results.totalReturn.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </div>
                </div>

                <div>
                  <div className="text-sm opacity-90">ROI</div>
                  <div className="text-3xl font-bold">
                    {results.roi.toFixed(2)}%
                  </div>
                </div>
              </div>
            </div>

            {/* Investment Breakdown */}
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <h4 className="text-lg font-semibold text-gray-900 mb-4">Investment Breakdown</h4>
              <div className="space-y-3">
                <div className="flex justify-between">
                  <span className="text-gray-600">Initial Investment:</span>
                  <span className="font-semibold">${investment.toLocaleString()}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Daily Rate:</span>
                  <span className="font-semibold">{dailyRate}%</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Duration:</span>
                  <span className="font-semibold">{duration} days</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Compounding:</span>
                  <span className="font-semibold">{compounding ? 'Yes' : 'No'}</span>
                </div>
              </div>
            </div>

            {/* Risk Disclaimer */}
            <div className="bg-red-50 border border-red-200 rounded-lg p-4">
              <h5 className="font-semibold text-red-800 mb-2">Risk Disclaimer</h5>
              <p className="text-sm text-red-700">
                All investments carry risk. Past performance does not guarantee future results. 
                Please invest responsibly and only what you can afford to lose.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Calculator;