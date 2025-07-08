import React, { useState, useEffect } from 'react';
import { 
  DollarSign, 
  TrendingUp, 
  Wallet,
  Eye,
  EyeOff,
  Plus,
  ArrowUpRight,
  ArrowDownLeft,
  BarChart3,
  LogOut,
  RefreshCw,
  AlertCircle
} from 'lucide-react';
import type { BlackCnoteApiSettings } from '../config/environment';
import { useLiveEditing } from '../hooks/useLiveEditing';

interface UserData {
  id: number;
  name: string;
  email: string;
  balance: number;
}

interface InvestmentPlan {
  id: number;
  name: string;
  return_rate: number;
  min_investment: number;
  max_investment: number;
  duration: number;
}

interface Investment {
  id: number;
  plan_name: string;
  amount: number;
  daily_return: number;
  daily_profit: number;
  days_left: number;
  total_days: number;
  status: string;
  created_at: string;
}

interface Transaction {
  id: number;
  type: string;
  amount: number;
  description: string;
  date: string;
  status: string;
}

// Mock data for development when backend is not available
const mockUserData: UserData = {
  id: 1,
  name: 'John Doe',
  email: 'john@example.com',
  balance: 15000.00
};

const mockInvestments: Investment[] = [
  {
    id: 1,
    plan_name: 'Premium Plan',
    amount: 5000.00,
    daily_return: 2.5,
    daily_profit: 125.00,
    days_left: 15,
    total_days: 30,
    status: 'active',
    created_at: '2024-01-15'
  },
  {
    id: 2,
    plan_name: 'Standard Plan',
    amount: 3000.00,
    daily_return: 1.8,
    daily_profit: 54.00,
    days_left: 8,
    total_days: 20,
    status: 'active',
    created_at: '2024-01-20'
  }
];

const mockTransactions: Transaction[] = [
  {
    id: 1,
    type: 'deposit',
    amount: 10000.00,
    description: 'Initial deposit',
    date: '2024-01-10',
    status: 'completed'
  },
  {
    id: 2,
    type: 'investment',
    amount: -5000.00,
    description: 'Premium Plan investment',
    date: '2024-01-15',
    status: 'completed'
  },
  {
    id: 3,
    type: 'profit',
    amount: 125.00,
    description: 'Daily profit from Premium Plan',
    date: '2024-01-16',
    status: 'completed'
  }
];

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

const Dashboard = () => {
  const [balanceVisible, setBalanceVisible] = useState(true);
  const [userData, setUserData] = useState<UserData | null>(null);
  const [investments, setInvestments] = useState<Investment[]>([]);
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showNewInvestmentModal, setShowNewInvestmentModal] = useState(false);
  const [showWithdrawModal, setShowWithdrawModal] = useState(false);

  const [liveEditingState, liveEditingActions] = useLiveEditing({
    onChange: (change) => {
      // Handle content/component changes
      if (change.type === 'content' && change.id === 'dashboard-user') {
        setUserData(change.content);
      }
      if (change.type === 'component' && change.name === 'dashboard-investments') {
        setInvestments(change.data);
      }
      if (change.type === 'component' && change.name === 'dashboard-transactions') {
        setTransactions(change.data);
      }
    }
  });

  // Fetch dashboard data from WordPress REST API
  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        setLoading(true);
        
        // Check if we're in development mode first
        const isDevelopment = process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost';
        
        if (isDevelopment) {
          setUserData(mockUserData);
          setInvestments(mockInvestments);
          setTransactions(mockTransactions);
          (window as { investmentPlans?: InvestmentPlan[] }).investmentPlans = mockPlans;
          setLoading(false);
          return;
        }
        
        // Get API settings from window object
        const apiSettings = (window as { blackCnoteApiSettings?: BlackCnoteApiSettings }).blackCnoteApiSettings;
        if (!apiSettings) {
          throw new Error('API settings not found');
        }

        // Check if we're in development mode using the new flag
        if (process.env.NODE_ENV === 'development') {
          // Use mock data for development
          setUserData(mockUserData);
          setInvestments(mockInvestments);
          setTransactions(mockTransactions);
          (window as { investmentPlans?: InvestmentPlan[] }).investmentPlans = mockPlans;
        } else {
          // Try to fetch real data from WordPress backend
          try {
            // Fetch user data
            const userResponse = await fetch(`${apiSettings.baseUrl}user-data`, {
              headers: {
                'X-WP-Nonce': apiSettings?.nonce || '',
              }
            });

            if (userResponse.ok) {
              const userData = await userResponse.json();
              setUserData(userData);
            }

            // Fetch investments
            const investmentsResponse = await fetch(`${apiSettings.baseUrl}investments`, {
              headers: {
                'X-WP-Nonce': apiSettings?.nonce || '',
              }
            });

            if (investmentsResponse.ok) {
              const investmentsData = await investmentsResponse.json();
              setInvestments(investmentsData);
            }

            // Fetch transactions
            const transactionsResponse = await fetch(`${apiSettings.baseUrl}transactions`, {
              headers: {
                'X-WP-Nonce': apiSettings?.nonce || '',
              }
            });

            if (transactionsResponse.ok) {
              const transactionsData = await transactionsResponse.json();
              setTransactions(transactionsData);
            }

            // Fetch investment plans
            const plansResponse = await fetch(`${apiSettings?.baseUrl ?? ''}plans`);
            if (plansResponse.ok) {
              const plansData: InvestmentPlan[] = await plansResponse.json();
              (window as { investmentPlans?: InvestmentPlan[] }).investmentPlans = plansData;
            }
          } catch (apiError) {
            if (process.env.NODE_ENV === 'development') console.warn('API calls failed, falling back to mock data:', apiError);
            // Fallback to mock data if API calls fail
            setUserData(mockUserData);
            setInvestments(mockInvestments);
            setTransactions(mockTransactions);
            (window as { investmentPlans?: InvestmentPlan[] }).investmentPlans = mockPlans;
          }
        }

      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to load dashboard data');
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  const calculateStats = () => {
    const totalInvested = investments.reduce((sum, i) => sum + i.amount, 0);
    const totalProfit = transactions.filter(t => t.type === 'profit').reduce((sum, t) => sum + t.amount, 0);
    const activeInvestments = investments.filter(i => i.status === 'active').length;
    return { totalInvested, totalProfit, activeInvestments };
  };

  const stats = calculateStats();

  const handleLogout = () => {
    window.location.href = '/wp-login.php?action=logout';
  };

  const handleNewInvestment = () => {
    setShowNewInvestmentModal(true);
  };

  const handleWithdraw = () => {
    setShowWithdrawModal(true);
  };

  const refreshData = () => {
    // This would re-trigger the useEffect to fetch data
    // For now, we'll just log it
    console.log('Refreshing data...');
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-screen">
        <div className="text-center">
          <RefreshCw className="h-12 w-12 text-blue-600 animate-spin" />
          <p className="mt-4 text-lg text-gray-600">Loading Dashboard...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center h-screen">
        <div className="text-center text-red-600">
          <AlertCircle className="h-12 w-12 mx-auto" />
          <p className="mt-4 text-lg">{error}</p>
        </div>
      </div>
    );
  }

  if (!userData) {
    return (
      <div className="flex items-center justify-center h-screen">
        <div className="text-center">
          <p className="text-lg text-gray-600">Could not load user data.</p>
          <a href="/wp-login.php" className="text-blue-600 hover:underline">Please login</a>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-100 p-4 sm:p-6 lg:p-8">
      {/* Header */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Welcome back, {userData.name}!</h1>
          <p className="text-gray-600 mt-1">Here's your investment overview.</p>
        </div>
        <div className="flex items-center space-x-2 mt-4 sm:mt-0">
          <button onClick={refreshData} className="p-2 rounded-full hover:bg-gray-200 transition">
            <RefreshCw className="h-5 w-5 text-gray-600" />
          </button>
          <button onClick={handleLogout} className="bg-red-500 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-red-600 transition">
            <LogOut className="h-4 w-4" />
            <span>Logout</span>
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {/* Total Balance */}
        <div className="bg-white p-6 rounded-2xl shadow-md">
          <div className="flex items-center justify-between mb-4">
            <div className="bg-gradient-to-br from-blue-500 to-blue-600 w-12 h-12 rounded-lg flex items-center justify-center">
              <Wallet className="h-6 w-6 text-white" />
            </div>
            <button onClick={() => setBalanceVisible(!balanceVisible)}>
              {balanceVisible ? <EyeOff className="h-5 w-5 text-gray-400" /> : <Eye className="h-5 w-5 text-gray-400" />}
            </button>
          </div>
          <p className="text-gray-500 text-sm">Total Balance</p>
          <p className="text-3xl font-bold text-gray-900">
            {balanceVisible ? `$${userData.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}` : '•••••••'}
          </p>
        </div>

        {/* Total Invested */}
        <div className="bg-white p-6 rounded-2xl shadow-md">
          <div className="bg-gradient-to-br from-green-500 to-green-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
            <TrendingUp className="h-6 w-6 text-white" />
          </div>
          <p className="text-gray-500 text-sm">Total Invested</p>
          <p className="text-3xl font-bold text-gray-900">${stats.totalInvested.toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
        </div>

        {/* Total Profit */}
        <div className="bg-white p-6 rounded-2xl shadow-md">
          <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
            <DollarSign className="h-6 w-6 text-white" />
          </div>
          <p className="text-gray-500 text-sm">Total Profit</p>
          <p className="text-3xl font-bold text-gray-900">${stats.totalProfit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
        </div>

        {/* Active Investments */}
        <div className="bg-white p-6 rounded-2xl shadow-md">
          <div className="bg-gradient-to-br from-indigo-500 to-indigo-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
            <BarChart3 className="h-6 w-6 text-white" />
          </div>
          <p className="text-gray-500 text-sm">Active Investments</p>
          <p className="text-3xl font-bold text-gray-900">{stats.activeInvestments}</p>
        </div>
      </div>
      
      {/* Main Content Area */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Left Column: Active Investments */}
        <div className="lg:col-span-2 bg-white p-6 rounded-2xl shadow-md">
          <h2 className="text-xl font-bold text-gray-900 mb-4">Active Investments</h2>
          <div className="space-y-4">
            {investments.length > 0 ? (
              investments.map(investment => (
                <div key={investment.id} className="bg-gray-50 p-4 rounded-lg">
                  <div className="flex justify-between items-center">
                    <div>
                      <p className="font-bold text-gray-800">{investment.plan_name}</p>
                      <p className="text-sm text-gray-500">Invested: ${investment.amount.toLocaleString()}</p>
                    </div>
                    <div className="text-right">
                      <p className="font-bold text-green-600">+${investment.daily_profit.toLocaleString()}/day</p>
                      <p className="text-sm text-gray-500">{investment.days_left} days left</p>
                    </div>
                  </div>
                  <div className="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div 
                      className="bg-green-500 h-2.5 rounded-full" 
                      style={{ width: `${((investment.total_days - investment.days_left) / investment.total_days) * 100}%` }}
                    ></div>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-gray-500">You have no active investments.</p>
            )}
          </div>
        </div>

        {/* Right Column: Quick Actions & Recent Transactions */}
        <div className="space-y-8">
          {/* Quick Actions */}
          <div className="bg-white p-6 rounded-2xl shadow-md">
            <h2 className="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
            <div className="flex space-x-4">
              <button onClick={handleNewInvestment} className="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg flex items-center justify-center space-x-2 hover:bg-blue-700 transition">
                <Plus className="h-5 w-5" />
                <span>New Investment</span>
              </button>
              <button onClick={handleWithdraw} className="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg flex items-center justify-center space-x-2 hover:bg-green-700 transition">
                <ArrowUpRight className="h-5 w-5" />
                <span>Withdraw</span>
              </button>
            </div>
          </div>
          
          {/* Recent Transactions */}
          <div className="bg-white p-6 rounded-2xl shadow-md">
            <h2 className="text-xl font-bold text-gray-900 mb-4">Recent Transactions</h2>
            <div className="space-y-3">
              {transactions.slice(0, 5).map(tx => (
                <div key={tx.id} className="flex justify-between items-center">
                  <div className="flex items-center space-x-3">
                    <div className={`p-2 rounded-full ${tx.type === 'deposit' || tx.type === 'profit' ? 'bg-green-100' : 'bg-red-100'}`}>
                      {tx.type === 'deposit' || tx.type === 'profit' ? (
                        <ArrowDownLeft className="h-4 w-4 text-green-600" />
                      ) : (
                        <ArrowUpRight className="h-4 w-4 text-red-600" />
                      )}
                    </div>
                    <div>
                      <p className="font-medium text-gray-800 capitalize">{tx.type}</p>
                      <p className="text-sm text-gray-500">{new Date(tx.date).toLocaleDateString()}</p>
                    </div>
                  </div>
                  <p className={`font-bold ${tx.amount > 0 ? 'text-green-600' : 'text-red-600'}`}>
                    {tx.amount > 0 ? '+' : ''}${tx.amount.toLocaleString()}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {showNewInvestmentModal && <NewInvestmentModal onClose={() => setShowNewInvestmentModal(false)} onSuccess={refreshData} />}
      {showWithdrawModal && <WithdrawModal onClose={() => setShowWithdrawModal(false)} onSuccess={refreshData} balance={userData.balance} />}
    </div>
  );
};

// New Investment Modal Component
const NewInvestmentModal = ({ onClose, onSuccess }: { onClose: () => void; onSuccess: () => void }) => {
  const [selectedPlan, setSelectedPlan] = useState<InvestmentPlan | null>(null);
  const [investmentAmount, setInvestmentAmount] = useState('');
  const [loading, setLoading] = useState(false);

  const plans = (window as { investmentPlans?: InvestmentPlan[] }).investmentPlans || [];

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedPlan || !investmentAmount) return;

    setLoading(true);
    try {
      await onSuccess();
    } catch (error) {
      if (process.env.NODE_ENV === 'development') console.error('Investment creation failed:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h2 className="text-2xl font-bold text-gray-900 mb-6">New Investment</h2>
        
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Select Plan
            </label>
            <select 
              value={selectedPlan?.id || ''} 
              onChange={(e) => setSelectedPlan(plans.find((p: InvestmentPlan) => p.id === parseInt(e.target.value)) || null)}
              className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              required
            >
              <option value="">Choose a plan...</option>
              {plans.map((plan: InvestmentPlan) => (
                <option key={plan.id} value={plan.id}>
                  {plan.name} - {plan.return_rate}% daily return
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Investment Amount
            </label>
            <input
              type="number"
              value={investmentAmount}
              onChange={(e) => setInvestmentAmount(e.target.value)}
              min={selectedPlan?.min_investment || 0}
              max={selectedPlan?.max_investment || 999999}
              step="0.01"
              className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              placeholder="Enter amount..."
              required
            />
            {selectedPlan && (
              <p className="text-sm text-gray-600 mt-1">
                Min: ${selectedPlan.min_investment} | Max: ${selectedPlan.max_investment}
              </p>
            )}
          </div>

          <div className="flex space-x-4">
            <button
              type="button"
              onClick={onClose}
              className="flex-1 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={loading}
              className="flex-1 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 disabled:opacity-50"
            >
              {loading ? 'Creating...' : 'Create Investment'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

// Withdraw Modal Component
const WithdrawModal = ({ onClose, onSuccess, balance }: { onClose: () => void; onSuccess: () => void; balance: number }) => {
  const [withdrawAmount, setWithdrawAmount] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!withdrawAmount || parseFloat(withdrawAmount) > balance) return;

    setLoading(true);
    try {
      await onSuccess();
    } catch (error) {
      if (process.env.NODE_ENV === 'development') console.error('Withdrawal failed:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h2 className="text-2xl font-bold text-gray-900 mb-6">Withdraw Funds</h2>
        
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Available Balance
            </label>
            <div className="text-2xl font-bold text-gray-900">
              ${balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Withdrawal Amount
            </label>
            <input
              type="number"
              value={withdrawAmount}
              onChange={(e) => setWithdrawAmount(e.target.value)}
              max={balance}
              step="0.01"
              className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              placeholder="Enter amount..."
              required
            />
          </div>

          <div className="flex space-x-4">
            <button
              type="button"
              onClick={onClose}
              className="flex-1 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={loading || parseFloat(withdrawAmount) > balance}
              className="flex-1 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 disabled:opacity-50"
            >
              {loading ? 'Processing...' : 'Withdraw'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default Dashboard;
