import React, { useState, useEffect } from 'react';
import { Calculator as CalcIcon, TrendingUp, DollarSign, Calendar, Percent, RefreshCw, AlertCircle } from 'lucide-react';
import type { BlackCnoteApiSettings } from '../config/environment';

interface CalculationResult {
  investment: number;
  daily_profit: number;
  total_profit: number;
  total_return: number;
  roi: number;
  duration: number;
}

interface InvestmentPlan {
  id: number;
  name: string;
  return_rate: number;
  min_investment: number;
  max_investment: number;
  duration: number;
}

// Mock data for development when backend is not available
const mockPlans: InvestmentPlan[] = [
  {
    id: 1,
    name: 'Starter Plan',
    return_rate: 1.2,
    min_investment: 100,
    max_investment: 1000,
    duration: 15
  },
  {
    id: 2,
    name: 'Standard Plan',
    return_rate: 1.8,
    min_investment: 1000,
    max_investment: 5000,
    duration: 20
  },
  {
    id: 3,
    name: 'Premium Plan',
    return_rate: 2.5,
    min_investment: 5000,
    max_investment: 50000,
    duration: 30
  }
];

const Calculator = () => {
  const [investment, setInvestment] = useState(1000);
  const [dailyRate, setDailyRate] = useState(2.0);
  const [duration, setDuration] = useState(30);
  const [compounding, setCompounding] = useState(false);
  const [results, setResults] = useState<CalculationResult | null>(null);
  const [plans, setPlans] = useState<InvestmentPlan[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Fetch investment plans from WordPress REST API
  useEffect(() => {
    const fetchPlans = async () => {
      try {
        // Check if we're in development mode first
        const isDevelopment = process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost';
        
        if (isDevelopment) {
          // Use mock data for development
          return;
        }
        
        const apiSettings = (window as { blackCnoteApiSettings?: BlackCnoteApiSettings }).blackCnoteApiSettings;
        if (!apiSettings) {
          throw new Error('API settings not found');
        }

        // Check if we're in development mode using the new flag
        const isDevelopmentMode = apiSettings?.isDevelopment === true;
        
        if (isDevelopmentMode) {
          // Use mock data for development
          setPlans(mockPlans);
        } else {
          // Try to fetch real data from WordPress backend
          try {
            const response = await fetch(`${apiSettings?.baseUrl ?? ''}plans`);
            if (response.ok) {
              const plansData = await response.json();
              setPlans(plansData);
            } else {
              throw new Error('Failed to fetch investment plans');
            }
          } catch (apiError) {
            if (process.env.NODE_ENV === 'development') console.warn('API call failed, falling back to mock data:', apiError);
            // Fallback to mock data if API call fails
            setPlans(mockPlans);
          }
        }

      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to load investment plans');
      }
    };

    fetchPlans();
  }, []);

  // Calculate returns using WordPress REST API
  useEffect(() => {
    const calculateReturns = async () => {
      try {
        setLoading(true);
        
        // Check if we're in development mode first
        const isDevelopment = process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost';
        
        if (isDevelopment) {
          // Use mock data for development
          return;
        }
        
        const apiSettings = (window as { blackCnoteApiSettings?: BlackCnoteApiSettings }).blackCnoteApiSettings;
        if (!apiSettings) {
          throw new Error('API settings not found');
        }

        // Check if we're in development mode using the new flag
        const isDevelopmentMode = apiSettings?.isDevelopment === true;
        
        if (isDevelopmentMode) {
          // Calculate locally in development mode
          const dailyProfit = (investment * dailyRate) / 100;
          const totalProfit = dailyProfit * duration;
          const totalReturn = investment + totalProfit;
          const roi = ((totalReturn - investment) / investment) * 100;
          
          const mockResult: CalculationResult = {
            investment,
            daily_profit: dailyProfit,
            total_profit: totalProfit,
            total_return: totalReturn,
            roi,
            duration
          };
          
          setResults(mockResult);
        } else {
          // Try to calculate via API
          try {
            const params = new URLSearchParams({
              amount: investment.toString(),
              daily_rate: dailyRate.toString(),
              duration: duration.toString(),
              compounding: compounding.toString(),
            });

            const response = await fetch(`${apiSettings?.baseUrl ?? ''}calculate?${params}`);
            if (response.ok) {
              const data = await response.json();
              setResults(data);
            } else {
              throw new Error('Failed to calculate returns');
            }
          } catch (apiError) {
            if (process.env.NODE_ENV === 'development') console.warn('API calculation failed, using local calculation:', apiError);
            // Fallback to local calculation
            const dailyProfit = (investment * dailyRate) / 100;
            const totalProfit = dailyProfit * duration;
            const totalReturn = investment + totalProfit;
            const roi = ((totalReturn - investment) / investment) * 100;
            
            const mockResult: CalculationResult = {
              investment,
              daily_profit: dailyProfit,
              total_profit: totalProfit,
              total_return: totalReturn,
              roi,
              duration
            };
            
            setResults(mockResult);
          }
        }

      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to calculate returns');
      } finally {
        setLoading(false);
      }
    };

    // Only calculate if we have valid inputs
    if (investment > 0 && dailyRate > 0 && duration > 0) {
      calculateReturns();
    }
  }, [investment, dailyRate, duration, compounding]);

  const selectPlan = (plan: InvestmentPlan) => {
    setDailyRate(plan.return_rate);
    setDuration(plan.duration);
    if (investment < plan.min_investment) {
      setInvestment(plan.min_investment);
    } else if (investment > plan.max_investment) {
      setInvestment(plan.max_investment);
    }
  };

  const handleInvestNow = () => {
    window.location.href = '/investment-plans';
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
                {plans.map((plan) => (
                  <button
                    key={plan.id}
                    onClick={() => selectPlan(plan)}
                    className="p-4 border-2 border-gray-200 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all duration-200 text-left"
                  >
                    <div className="font-semibold text-gray-900">{plan.name}</div>
                    <div className="text-yellow-600 font-bold">{plan.return_rate}% Daily</div>
                    <div className="text-sm text-gray-600">{plan.duration} days</div>
                    <div className="text-sm text-gray-500">
                      ${plan.min_investment.toLocaleString()} - ${plan.max_investment.toLocaleString()}
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
              
              {loading ? (
                <div className="text-center py-8">
                  <RefreshCw className="h-8 w-8 animate-spin mx-auto mb-4" />
                  <p>Calculating...</p>
                </div>
              ) : error ? (
                <div className="text-center py-8">
                  <AlertCircle className="h-8 w-8 mx-auto mb-4" />
                  <p className="text-sm">{error}</p>
                </div>
              ) : results ? (
                <div className="space-y-6">
                  <div>
                    <div className="text-sm opacity-90">Daily Profit</div>
                    <div className="text-3xl font-bold">
                      ${results.daily_profit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </div>
                  </div>
                  
                  <div>
                    <div className="text-sm opacity-90">Total Profit</div>
                    <div className="text-3xl font-bold">
                      ${results.total_profit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </div>
                  </div>
                  
                  <div>
                    <div className="text-sm opacity-90">Total Return</div>
                    <div className="text-3xl font-bold">
                      ${results.total_return.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </div>
                  </div>
                  
                  <div>
                    <div className="text-sm opacity-90">ROI</div>
                    <div className="text-3xl font-bold">
                      {results.roi.toFixed(2)}%
                    </div>
                  </div>
                  
                  <div>
                    <div className="text-sm opacity-90">Duration</div>
                    <div className="text-xl font-semibold">
                      {results.duration} days
                    </div>
                  </div>
                  
                  <button
                    onClick={handleInvestNow}
                    className="w-full bg-white text-yellow-600 py-3 px-6 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200"
                  >
                    Invest Now
                  </button>
                </div>
              ) : (
                <div className="text-center py-8">
                  <CalcIcon className="h-12 w-12 opacity-50 mx-auto mb-4" />
                  <p className="text-sm opacity-90">Enter values to see your potential returns</p>
                </div>
              )}
            </div>

            {/* Investment Tips */}
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Investment Tips</h3>
              <div className="space-y-3 text-sm text-gray-600">
                <div className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                  <p>Start with smaller amounts to test the platform</p>
                </div>
                <div className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                  <p>Diversify your investments across different plans</p>
                </div>
                <div className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                  <p>Monitor your investments regularly</p>
                </div>
                <div className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                  <p>Never invest more than you can afford to lose</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Calculator;
