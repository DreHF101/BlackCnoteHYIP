import React, { useState } from 'react';
import { 
  DollarSign, 
  TrendingUp, 
  Wallet, 
  Clock, 
  Eye, 
  EyeOff, 
  Plus, 
  ArrowUpRight, 
  ArrowDownLeft,
  Calendar,
  BarChart3
} from 'lucide-react';

const Dashboard = () => {
  const [balanceVisible, setBalanceVisible] = useState(true);

  const stats = [
    {
      title: 'Total Balance',
      value: '$12,450.00',
      change: '+12.5%',
      changeType: 'positive',
      icon: Wallet,
    },
    {
      title: 'Active Investments',
      value: '$8,500.00',
      change: '+8.2%',
      changeType: 'positive',
      icon: TrendingUp,
    },
    {
      title: 'Total Profit',
      value: '$1,950.00',
      change: '+15.3%',
      changeType: 'positive',
      icon: DollarSign,
    },
    {
      title: 'Daily Earnings',
      value: '$127.50',
      change: '+2.1%',
      changeType: 'positive',
      icon: Clock,
    },
  ];

  const investments = [
    {
      plan: 'Growth Plan',
      amount: '$5,000.00',
      dailyReturn: '2.0%',
      dailyProfit: '$100.00',
      daysLeft: 32,
      totalDays: 45,
      status: 'active',
    },
    {
      plan: 'Premium Plan',
      amount: '$3,500.00',
      dailyReturn: '2.5%',
      dailyProfit: '$87.50',
      daysLeft: 45,
      totalDays: 60,
      status: 'active',
    },
  ];

  const transactions = [
    {
      type: 'deposit',
      amount: '$5,000.00',
      description: 'Investment in Growth Plan',
      date: '2024-01-15',
      status: 'completed',
    },
    {
      type: 'profit',
      amount: '$100.00',
      description: 'Daily profit from Growth Plan',
      date: '2024-01-14',
      status: 'completed',
    },
    {
      type: 'deposit',
      amount: '$3,500.00',
      description: 'Investment in Premium Plan',
      date: '2024-01-10',
      status: 'completed',
    },
    {
      type: 'profit',
      amount: '$87.50',
      description: 'Daily profit from Premium Plan',
      date: '2024-01-14',
      status: 'completed',
    },
  ];

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p className="text-gray-600 mt-1">Welcome back! Here's your investment overview.</p>
          </div>
          <div className="flex space-x-4 mt-4 sm:mt-0">
            <button className="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 flex items-center space-x-2">
              <Plus className="h-5 w-5" />
              <span>New Investment</span>
            </button>
            <button className="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors duration-200">
              Withdraw
            </button>
          </div>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {stats.map((stat, index) => (
            <div key={index} className="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
              <div className="flex items-center justify-between mb-4">
                <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-12 h-12 rounded-lg flex items-center justify-center">
                  <stat.icon className="h-6 w-6 text-white" />
                </div>
                <button
                  onClick={() => setBalanceVisible(!balanceVisible)}
                  className="text-gray-400 hover:text-gray-600"
                >
                  {balanceVisible ? <Eye className="h-5 w-5" /> : <EyeOff className="h-5 w-5" />}
                </button>
              </div>
              <div>
                <h3 className="text-sm font-medium text-gray-600 mb-1">{stat.title}</h3>
                <div className="text-2xl font-bold text-gray-900 mb-2">
                  {balanceVisible ? stat.value : '••••••'}
                </div>
                <div className={`text-sm flex items-center space-x-1 ${
                  stat.changeType === 'positive' ? 'text-green-600' : 'text-red-600'
                }`}>
                  <TrendingUp className="h-4 w-4" />
                  <span>{stat.change}</span>
                </div>
              </div>
            </div>
          ))}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Active Investments */}
          <div className="lg:col-span-2 bg-white rounded-2xl shadow-lg p-8">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-2xl font-semibold text-gray-900">Active Investments</h2>
              <BarChart3 className="h-6 w-6 text-gray-400" />
            </div>
            <div className="space-y-6">
              {investments.map((investment, index) => (
                <div key={index} className="border border-gray-200 rounded-lg p-6">
                  <div className="flex items-center justify-between mb-4">
                    <div>
                      <h3 className="text-lg font-semibold text-gray-900">{investment.plan}</h3>
                      <p className="text-gray-600">Investment Amount: {investment.amount}</p>
                    </div>
                    <div className="text-right">
                      <div className="text-2xl font-bold text-green-600">{investment.dailyReturn}</div>
                      <div className="text-sm text-gray-600">Daily Return</div>
                    </div>
                  </div>
                  
                  <div className="grid grid-cols-2 gap-4 mb-4">
                    <div>
                      <div className="text-sm text-gray-600">Daily Profit</div>
                      <div className="text-lg font-semibold text-gray-900">{investment.dailyProfit}</div>
                    </div>
                    <div>
                      <div className="text-sm text-gray-600">Days Remaining</div>
                      <div className="text-lg font-semibold text-gray-900">{investment.daysLeft} days</div>
                    </div>
                  </div>

                  {/* Progress Bar */}
                  <div className="space-y-2">
                    <div className="flex justify-between text-sm text-gray-600">
                      <span>Progress</span>
                      <span>{Math.round(((investment.totalDays - investment.daysLeft) / investment.totalDays) * 100)}%</span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        className="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full transition-all duration-300"
                        style={{ width: `${((investment.totalDays - investment.daysLeft) / investment.totalDays) * 100}%` }}
                      ></div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Recent Transactions */}
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-xl font-semibold text-gray-900">Recent Transactions</h2>
              <Calendar className="h-6 w-6 text-gray-400" />
            </div>
            <div className="space-y-4">
              {transactions.map((transaction, index) => (
                <div key={index} className="flex items-center space-x-4 p-4 border border-gray-100 rounded-lg">
                  <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                    transaction.type === 'deposit' 
                      ? 'bg-blue-100 text-blue-600' 
                      : 'bg-green-100 text-green-600'
                  }`}>
                    {transaction.type === 'deposit' ? (
                      <ArrowDownLeft className="h-5 w-5" />
                    ) : (
                      <ArrowUpRight className="h-5 w-5" />
                    )}
                  </div>
                  <div className="flex-1">
                    <div className="font-medium text-gray-900">{transaction.amount}</div>
                    <div className="text-sm text-gray-600">{transaction.description}</div>
                    <div className="text-xs text-gray-500">{transaction.date}</div>
                  </div>
                  <div className={`px-2 py-1 rounded-full text-xs font-medium ${
                    transaction.status === 'completed' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {transaction.status}
                  </div>
                </div>
              ))}
            </div>
            <button className="w-full mt-6 text-yellow-600 hover:text-yellow-700 font-medium text-sm">
              View All Transactions
            </button>
          </div>
        </div>

        {/* Quick Actions */}
        <div className="mt-8 bg-white rounded-2xl shadow-lg p-8">
          <h2 className="text-2xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all duration-200 text-center">
              <Plus className="h-8 w-8 text-gray-400 mx-auto mb-3" />
              <div className="font-medium text-gray-900">Make New Investment</div>
              <div className="text-sm text-gray-600">Start earning with a new plan</div>
            </button>
            <button className="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all duration-200 text-center">
              <ArrowUpRight className="h-8 w-8 text-gray-400 mx-auto mb-3" />
              <div className="font-medium text-gray-900">Withdraw Profits</div>
              <div className="text-sm text-gray-600">Transfer earnings to your account</div>
            </button>
            <button className="p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all duration-200 text-center">
              <BarChart3 className="h-8 w-8 text-gray-400 mx-auto mb-3" />
              <div className="font-medium text-gray-900">View Analytics</div>
              <div className="text-sm text-gray-600">Detailed investment reports</div>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;