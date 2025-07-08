import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/Header';
import Footer from './components/Footer';
import DebugMonitor from './components/DebugMonitor';
import DebugBanner from './components/DebugBanner';
import HomePage from './pages/HomePage';
import About from './pages/About';
import Contact from './pages/Contact';
import Dashboard from './pages/Dashboard';
import InvestmentPlans from './pages/InvestmentPlans';
import Calculator from './pages/Calculator';
import Transactions from './pages/Transactions';
import Profile from './pages/Profile';
import './index.css';
import type { WordPressSettings } from './types';
import { createRouterConfig } from './config/router-config';

// Test comment for React hot reload - this should trigger hot reload when saved

const App: React.FC<{ settings: WordPressSettings }> = ({ settings }) => {
  // Use the new router configuration that handles basename conflicts
  const routerConfig = createRouterConfig();
  const isDevelopment = process.env.NODE_ENV === 'development';
  const debugBanner = isDevelopment ? <DebugBanner isDevelopment={isDevelopment} /> : null;
  const debugComponent = isDevelopment ? <DebugMonitor isDevelopment={isDevelopment} /> : null;

  return (
    <Router basename={routerConfig.basename}>
      <div className="min-h-screen bg-gray-50 flex flex-col">
        {/* Debug Banner - shows monitoring status at top */}
        {debugBanner}
        
        {/* Debug Monitor - only shows in development */}
        {debugComponent}
        
        <Header />
        <main className="flex-grow">
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/investment-plans" element={<InvestmentPlans />} />
            <Route path="/calculator" element={<Calculator />} />
            <Route path="/about" element={<About />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/dashboard" element={<Dashboard />} />
            <Route path="/transactions" element={<Transactions />} />
            <Route path="/profile" element={<Profile />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </Router>
  );
}

export default App;

