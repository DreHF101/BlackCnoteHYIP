import React, { useState } from 'react';
import { Check, Star, TrendingUp, Shield, Clock, DollarSign } from 'lucide-react';

const InvestmentPlans = () => {
  const [selectedPlan, setSelectedPlan] = useState<number | null>(null);

  const plans = [
    {
      id: 1,
      name: 'Starter Plan',
      subtitle: 'Perfect for beginners',
      minInvestment: 100,
      maxInvestment: 999,
      dailyReturn: 1.5,
      duration: 30,
      totalReturn: 145,
      features: [
        'Daily profit withdrawal',
        'Principal back after 30 days',
        '24/7 customer support',
        'Secure SSL encryption',
        'Mobile app access',
      ],
      popular: false,
      color: 'from-blue-500 to-blue-600',
    },
    {
      id: 2,
      name: 'Growth Plan',
      subtitle: 'Most popular choice',
      minInvestment: 1000,
      maxInvestment: 4999,
      dailyReturn: 2.0,
      duration: 45,
      totalReturn: 190,
      features: [
        'Daily profit withdrawal',
        'Principal back after 45 days',
        'Priority customer support',
        'Advanced security features',
        'Mobile app access',
        'Investment analytics',
      ],
      popular: true,
      color: 'from-yellow-500 to-yellow-600',
    },
    {
      id: 3,
      name: 'Premium Plan',
      subtitle: 'For serious investors',
      minInvestment: 5000,
      maxInvestment: 19999,
      dailyReturn: 2.5,
      duration: 60,
      totalReturn: 250,
      features: [
        'Daily profit withdrawal',
        'Principal back after 60 days',
        'VIP customer support',
        'Advanced security features',
        'Mobile app access',
        'Investment analytics',
        'Personal account manager',
      ],
      popular: false,
      color: 'from-purple-500 to-purple-600',
    },
    {
      id: 4,
      name: 'Elite Plan',
      subtitle: 'Maximum returns',
      minInvestment: 20000,
      maxInvestment: 100000,
      dailyReturn: 3.0,
      duration: 90,
      totalReturn: 370,
      features: [
        'Daily profit withdrawal',
        'Principal back after 90 days',
        'Dedicated support team',
        'Bank-level security',
        'Mobile app access',
        'Advanced analytics',
        'Personal account manager',
        'Exclusive investment opportunities',
      ],
      popular: false,
      color: 'from-green-500 to-green-600',
    },
  ];

  const benefits = [
    {
      icon: Shield,
      title: 'Secure Investments',
      description: 'Your funds are protected with bank-level security and SSL encryption.',
    },
    {
      icon: TrendingUp,
      title: 'Guaranteed Returns',
      description: 'Enjoy consistent daily returns with our proven investment strategies.',
    },
    {
      icon: Clock,
      title: 'Flexible Duration',
      description: 'Choose investment periods that match your financial goals.',
    },
    {
      icon: DollarSign,
      title: 'Daily Withdrawals',
      description: 'Access your profits daily with instant withdrawal options.',
    },
  ];

  return (
    <div className="min-h-screen bg-gray-50 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center space-y-4 mb-16">
          <h1 className="text-4xl font-bold text-gray-900">Investment Plans</h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Choose from our carefully designed investment plans that offer competitive returns 
            while supporting Black-owned businesses and community projects.
          </p>
        </div>

        {/* Benefits Section */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
          {benefits.map((benefit, index) => (
            <div key={index} className="text-center space-y-4">
              <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                <benefit.icon className="h-8 w-8 text-white" />
              </div>
              <h3 className="text-lg font-semibold text-gray-900">{benefit.title}</h3>
              <p className="text-gray-600">{benefit.description}</p>
            </div>
          ))}
        </div>

        {/* Investment Plans */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {plans.map((plan) => (
            <div
              key={plan.id}
              className={`bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden ${
                plan.popular ? 'ring-2 ring-yellow-500 transform scale-105' : ''
              } ${selectedPlan === plan.id ? 'ring-2 ring-blue-500' : ''}`}
            >
              {plan.popular && (
                <div className={`bg-gradient-to-r ${plan.color} text-white text-center py-3 font-semibold flex items-center justify-center space-x-2`}>
                  <Star className="h-4 w-4" />
                  <span>Most Popular</span>
                </div>
              )}
              
              <div className="p-8 space-y-6">
                {/* Plan Header */}
                <div className="text-center space-y-2">
                  <h3 className="text-2xl font-bold text-gray-900">{plan.name}</h3>
                  <p className="text-gray-600">{plan.subtitle}</p>
                  <div className={`text-4xl font-bold bg-gradient-to-r ${plan.color} bg-clip-text text-transparent`}>
                    {plan.dailyReturn}%
                  </div>
                  <div className="text-gray-600">Daily Return</div>
                </div>

                {/* Plan Details */}
                <div className="space-y-3">
                  <div className="flex justify-between items-center">
                    <span className="text-gray-600">Min Investment:</span>
                    <span className="font-semibold">${plan.minInvestment.toLocaleString()}</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-600">Max Investment:</span>
                    <span className="font-semibold">${plan.maxInvestment.toLocaleString()}</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-600">Duration:</span>
                    <span className="font-semibold">{plan.duration} days</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-600">Total Return:</span>
                    <span className="font-semibold text-green-600">{plan.totalReturn}%</span>
                  </div>
                </div>

                {/* Features */}
                <div className="space-y-3">
                  <h4 className="font-semibold text-gray-900">Features:</h4>
                  <ul className="space-y-2">
                    {plan.features.map((feature, index) => (
                      <li key={index} className="flex items-center space-x-3">
                        <Check className="h-4 w-4 text-green-500 flex-shrink-0" />
                        <span className="text-sm text-gray-600">{feature}</span>
                      </li>
                    ))}
                  </ul>
                </div>

                {/* Action Button */}
                <button
                  onClick={() => setSelectedPlan(plan.id)}
                  className={`w-full py-3 rounded-lg font-semibold transition-all duration-200 ${
                    plan.popular
                      ? `bg-gradient-to-r ${plan.color} text-white hover:opacity-90`
                      : selectedPlan === plan.id
                      ? 'bg-blue-600 text-white'
                      : `border-2 border-gray-300 text-gray-700 hover:bg-gradient-to-r hover:${plan.color} hover:text-white hover:border-transparent`
                  }`}
                >
                  {selectedPlan === plan.id ? 'Selected' : 'Select Plan'}
                </button>
              </div>
            </div>
          ))}
        </div>

        {/* Investment Process */}
        <div className="mt-20 bg-white rounded-2xl shadow-lg p-8">
          <h2 className="text-3xl font-bold text-gray-900 text-center mb-12">How It Works</h2>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="text-center space-y-4">
              <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto text-white text-2xl font-bold">
                1
              </div>
              <h3 className="text-xl font-semibold text-gray-900">Register</h3>
              <p className="text-gray-600">Create your account with basic information and verify your identity.</p>
            </div>
            <div className="text-center space-y-4">
              <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto text-white text-2xl font-bold">
                2
              </div>
              <h3 className="text-xl font-semibold text-gray-900">Add Funds</h3>
              <p className="text-gray-600">Deposit funds to your account using secure payment methods.</p>
            </div>
            <div className="text-center space-y-4">
              <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto text-white text-2xl font-bold">
                3
              </div>
              <h3 className="text-xl font-semibold text-gray-900">Select Plan</h3>
              <p className="text-gray-600">Choose an investment plan that matches your goals and budget.</p>
            </div>
            <div className="text-center space-y-4">
              <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto text-white text-2xl font-bold">
                4
              </div>
              <h3 className="text-xl font-semibold text-gray-900">Enjoy Returns</h3>
              <p className="text-gray-600">Watch your investment grow with daily returns and withdraw profits anytime.</p>
            </div>
          </div>
        </div>

        {/* Risk Disclaimer */}
        <div className="mt-12 bg-red-50 border border-red-200 rounded-lg p-6">
          <h3 className="font-semibold text-red-800 mb-3">Important Risk Disclaimer</h3>
          <p className="text-red-700 leading-relaxed">
            All investments carry inherent risks, and past performance does not guarantee future results. 
            High-yield investment programs involve significant risk and may not be suitable for all investors. 
            Please carefully consider your financial situation and risk tolerance before investing. 
            Only invest funds that you can afford to lose. BlackCnote is committed to transparency and 
            responsible investing practices within the Black community.
          </p>
        </div>
      </div>
    </div>
  );
};

export default InvestmentPlans;