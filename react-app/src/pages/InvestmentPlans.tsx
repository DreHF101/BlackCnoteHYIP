import React, { useState, useEffect } from 'react';
import { 
  TrendingUp, 
  Clock, 
  DollarSign, 
  CheckCircle, 
  Calculator,
  ArrowRight,
  RefreshCw,
  AlertCircle
} from 'lucide-react';
import type { BlackCnoteApiSettings } from '../config/environment';

interface InvestmentPlan {
  id: number;
  name: string;
  return_rate: number;
  min_investment: number;
  max_investment: number;
  duration: number;
  description?: string;
  features?: string[];
}

interface CalculationResult {
  investment: number;
  dailyReturn: number;
  totalReturn: number;
  profit: number;
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
    duration: 15,
    description: 'Perfect for beginners. Start with a small investment and earn 1.2% daily returns.',
    features: ['1.2% Daily Return', '15 Days Duration', 'Min: $100', 'Max: $1,000', '24/7 Support', 'Instant Activation']
  },
  {
    id: 2,
    name: 'Standard Plan',
    return_rate: 1.8,
    min_investment: 1000,
    max_investment: 5000,
    duration: 20,
    description: 'Our most popular plan. Earn 1.8% daily returns with balanced risk and reward.',
    features: ['1.8% Daily Return', '20 Days Duration', 'Min: $1,000', 'Max: $5,000', '24/7 Support', 'Instant Activation']
  },
  {
    id: 3,
    name: 'Premium Plan',
    return_rate: 2.5,
    min_investment: 5000,
    max_investment: 50000,
    duration: 30,
    description: 'High returns for serious investors. Earn 2.5% daily returns with our premium strategy.',
    features: ['2.5% Daily Return', '30 Days Duration', 'Min: $5,000', 'Max: $50,000', '24/7 Support', 'Instant Activation']
  }
];

const InvestmentPlans = () => {
  const [plans, setPlans] = useState<InvestmentPlan[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedPlan, setSelectedPlan] = useState<InvestmentPlan | null>(null);
  const [investmentAmount, setInvestmentAmount] = useState('');
  const [calculationResult, setCalculationResult] = useState<CalculationResult | null>(null);
  const [showInvestmentModal, setShowInvestmentModal] = useState(false);
  const [isLoggedIn] = useState(false);

  // Fetch investment plans from WordPress REST API
  useEffect(() => {
    const fetchPlans = async () => {
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
        const isDevelopmentMode = apiSettings.isDevelopment === true;
        
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
            }

            // Fetch user data for balance
            const userResponse = await fetch(`${apiSettings.baseUrl}user-data`, {
              headers: {
                'X-WP-Nonce': apiSettings?.nonce || '',
              }
            });

            if (userResponse.ok) {
              // User data loaded
            }
          } catch (apiError) {
            if (process.env.NODE_ENV === 'development') console.warn('API calls failed, falling back to mock data:', apiError);
            // Fallback to mock data if API calls fail
            setPlans(mockPlans);
          }
        }

      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to load investment plans');
      } finally {
        setLoading(false);
      }
    };

    fetchPlans();
  }, []);

  const calculateReturn = (plan: InvestmentPlan, amount: number): CalculationResult => {
    const dailyReturn = (amount * plan.return_rate) / 100;
    const totalReturn = dailyReturn * plan.duration;
    const profit = totalReturn - amount;

    return {
      investment: amount,
      dailyReturn,
      totalReturn,
      profit,
      duration: plan.duration
    };
  };

  const handlePlanSelect = (plan: InvestmentPlan) => {
    setSelectedPlan(plan);
    setInvestmentAmount(plan.min_investment.toString());
    setCalculationResult(calculateReturn(plan, plan.min_investment));
  };

  const handleAmountChange = (amount: string) => {
    setInvestmentAmount(amount);
    if (selectedPlan) {
      const numAmount = parseFloat(amount) || 0;
      setCalculationResult(calculateReturn(selectedPlan, numAmount));
    }
  };

  const handleInvestNow = (plan: InvestmentPlan) => {
    if (!isLoggedIn) {
      // Redirect to WordPress login
      window.location.href = '/wp-login.php';
      return;
    }
    
    setSelectedPlan(plan);
    setInvestmentAmount(plan.min_investment.toString());
    setCalculationResult(calculateReturn(plan, plan.min_investment));
    setShowInvestmentModal(true);
  };

  const handleCreateInvestment = async () => {
    if (!selectedPlan || !investmentAmount) return;

    const amount = parseFloat(investmentAmount);
    if (amount < selectedPlan.min_investment || amount > selectedPlan.max_investment) {
      alert('Investment amount is outside the allowed range.');
      return;
    }

    try {
      const apiSettings = (window as { blackCnoteApiSettings?: BlackCnoteApiSettings }).blackCnoteApiSettings;
      const response = await fetch(`${apiSettings?.baseUrl ?? ''}invest`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': apiSettings?.nonce || '',
        },
        body: JSON.stringify({
          plan_id: selectedPlan.id,
          amount: amount,
        }),
      });

      if (response.ok) {
        const result = await response.json();
        alert(result.message || 'Investment created successfully!');
        setShowInvestmentModal(false);
        
        // Redirect to dashboard
        window.location.href = '/dashboard';
      } else {
        const error = await response.json();
        alert(error.message || 'Failed to create investment. Please try again.');
      }
    } catch (error) {
      alert('Failed to create investment. Please try again.');
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <RefreshCw className="h-8 w-8 text-yellow-500 animate-spin mx-auto mb-4" />
          <p className="text-gray-600">Loading investment plans...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <AlertCircle className="h-8 w-8 text-red-500 mx-auto mb-4" />
          <p className="text-red-600 mb-4">{error}</p>
          <button 
            onClick={() => window.location.reload()}
            className="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-16">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center mb-16">
          <h1 className="text-5xl font-extrabold text-gray-900 mb-4">
            Our Investment Plans
          </h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Choose from our carefully crafted investment plans designed to maximize your returns 
            while maintaining security and transparency.
          </p>
        </div>

        {/* Plans Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
          {plans.map((plan) => (
            <div 
              key={plan.id}
              className={`bg-white rounded-2xl shadow-xl p-8 flex flex-col justify-between transition-all duration-300 hover:shadow-2xl hover:scale-105 ${selectedPlan?.id === plan.id ? 'ring-4 ring-yellow-400' : ''}`}
              onClick={() => handlePlanSelect(plan)}
            >
              <div className="flex-grow">
                <div className="text-center">
                  <h3 className="text-2xl font-bold mb-2">{plan.name}</h3>
                  <p className="text-5xl font-bold text-yellow-500 mb-4">{plan.return_rate}%</p>
                  <p className="text-sm text-gray-500 mb-6">Daily Return for {plan.duration} Days</p>
                  <ul className="space-y-3 text-left mb-8">
                    {plan.features?.map((feature, index) => (
                      <li key={index} className="flex items-center text-sm text-gray-600">
                        <CheckCircle className="h-4 w-4 text-green-500 mr-2 flex-shrink-0" />
                        {feature}
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
              <button 
                onClick={() => handleInvestNow(plan)}
                className="w-full bg-yellow-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-yellow-600 transition-colors duration-300 flex items-center justify-center space-x-2"
              >
                <span>Invest Now</span>
                <ArrowRight className="h-5 w-5" />
              </button>
            </div>
          ))}
        </div>

        {/* Calculator and Details Section */}
        <div className="flex flex-wrap -mx-4">
          {/* Selected Plan Details */}
          <div className="w-full lg:w-2/3 px-4 mb-8 lg:mb-0">
            <div className="bg-white rounded-2xl shadow-xl p-8">
              <h2 className="text-3xl font-bold text-gray-900 mb-6">
                Selected Plan Details: {selectedPlan?.name || 'No Plan Selected'}
              </h2>
              {selectedPlan ? (
                <div>
                  <p className="text-gray-600 mb-6">{selectedPlan.description}</p>
                  <div className="grid grid-cols-2 gap-4 text-gray-700">
                    <p className="flex items-center"><DollarSign className="h-5 w-5 mr-2 text-yellow-500"/><strong>Min. Investment:</strong> ${selectedPlan.min_investment.toLocaleString()}</p>
                    <p className="flex items-center"><DollarSign className="h-5 w-5 mr-2 text-yellow-500"/><strong>Max. Investment:</strong> ${selectedPlan.max_investment.toLocaleString()}</p>
                    <p className="flex items-center"><TrendingUp className="h-5 w-5 mr-2 text-yellow-500"/><strong>Daily Return:</strong> {selectedPlan.return_rate}%</p>
                    <p className="flex items-center"><Clock className="h-5 w-5 mr-2 text-yellow-500"/><strong>Duration:</strong> {selectedPlan.duration} days</p>
                  </div>
                </div>
              ) : (
                <p className="text-gray-500">Please select a plan from the list above to see its details.</p>
              )}
            </div>
          </div>

          {/* Investment Calculator */}
          <div className="w-full lg:w-1/3 px-4">
            <div className="bg-white rounded-2xl shadow-xl p-8">
              <h2 className="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                <Calculator className="h-8 w-8 mr-3 text-yellow-500" />
                Calculator
              </h2>
              <div className="space-y-4">
                <div>
                  <label htmlFor="investmentAmount" className="block text-sm font-medium text-gray-700 mb-1">
                    Investment Amount
                  </label>
                  <div className="relative">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <DollarSign className="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                      type="number"
                      id="investmentAmount"
                      value={investmentAmount}
                      onChange={(e) => handleAmountChange(e.target.value)}
                      placeholder="Enter amount"
                      className="mt-1 block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                      disabled={!selectedPlan}
                    />
                  </div>
                </div>
                {calculationResult ? (
                  <div className="bg-gray-50 rounded-lg p-4">
                    <h3 className="font-bold text-lg mb-2 text-gray-800">Potential Returns</h3>
                    <div className="space-y-2 text-sm">
                      <p className="flex justify-between items-center"><span>Daily Return:</span> <strong className="text-lg">${calculationResult.dailyReturn.toFixed(2)}</strong></p>
                      <p className="flex justify-between items-center"><span>Total Return:</span> <strong className="text-lg">${calculationResult.totalReturn.toFixed(2)}</strong></p>
                      <p className="flex justify-between items-center"><span>Total Profit:</span> <strong className="text-lg text-green-600">${calculationResult.profit.toFixed(2)}</strong></p>
                    </div>
                  </div>
                ) : (
                  <div className="bg-gray-50 rounded-lg p-6 text-center">
                    <Calculator className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                    <p className="text-gray-600">Select a plan and enter amount to see your potential returns</p>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Why Choose Us Section */}
        <div className="mt-16">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-extrabold text-gray-900 mb-4">
              Why Choose Us?
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              We provide a secure, transparent, and profitable investment environment.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div className="bg-white p-8 rounded-2xl shadow-xl">
              <h3 className="text-xl font-bold mb-2">Secure Investments</h3>
              <p className="text-gray-600">Your funds are protected with top-tier security measures.</p>
            </div>
            <div className="bg-white p-8 rounded-2xl shadow-xl">
              <h3 className="text-xl font-bold mb-2">High Returns</h3>
              <p className="text-gray-600">We offer competitive returns on your investments.</p>
            </div>
            <div className="bg-white p-8 rounded-2xl shadow-xl">
              <CheckCircle className="h-12 w-12 text-yellow-500 mx-auto mb-4" />
              <h3 className="text-xl font-bold mb-2">Easy to Use</h3>
              <p className="text-gray-600">Our platform is designed for both beginners and experts.</p>
            </div>
          </div>
        </div>
      </div>

      {showInvestmentModal && selectedPlan && (
        <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            <h2 className="text-2xl font-bold mb-4">Invest in {selectedPlan.name}</h2>
            <p className="mb-4">You are about to invest <strong className="text-yellow-600">${parseFloat(investmentAmount).toLocaleString()}</strong> in the {selectedPlan.name}.</p>
            <div className="bg-gray-100 p-4 rounded-lg mb-6">
              <p className="flex justify-between"><span>Daily Return:</span> <strong>${calculationResult?.dailyReturn.toFixed(2)}</strong></p>
              <p className="flex justify-between"><span>Total Profit:</span> <strong className="text-green-600">${calculationResult?.profit.toFixed(2)}</strong></p>
            </div>
            <div className="flex justify-end space-x-4">
              <button
                onClick={() => setShowInvestmentModal(false)}
                className="px-4 py-2 rounded-lg text-gray-600 bg-gray-200 hover:bg-gray-300"
              >
                Cancel
              </button>
              <button
                onClick={handleCreateInvestment}
                className="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600"
              >
                Confirm Investment
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default InvestmentPlans; 
