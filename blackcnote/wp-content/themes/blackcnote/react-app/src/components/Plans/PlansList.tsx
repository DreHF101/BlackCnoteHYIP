import React, { useState, useEffect } from 'react';
import { hyiplabAPI, Plan } from '../../api/hyiplab';
import PlanCard from './PlanCard';
import './PlansList.css';

export const PlansList: React.FC = () => {
  const [plans, setPlans] = useState<Plan[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchPlans = async () => {
      try {
        setLoading(true);
        const data = await hyiplabAPI.getPlans();
        setPlans(data);
        setError(null);
      } catch (err) {
        console.error('Error fetching plans:', err);
        setError('Failed to load investment plans. Please try again later.');
      } finally {
        setLoading(false);
      }
    };

    fetchPlans();
  }, []);

  if (loading) {
    return (
      <div className="plans-loading">
        <div className="loading-spinner"></div>
        <p>Loading investment plans...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="plans-error">
        <p>{error}</p>
        <button onClick={() => window.location.reload()}>Retry</button>
      </div>
    );
  }

  if (plans.length === 0) {
    return (
      <div className="plans-empty">
        <p>No investment plans available at the moment.</p>
      </div>
    );
  }

  return (
    <div className="plans-container">
      <div className="plans-header">
        <h2>Investment Plans</h2>
        <p>Choose from our carefully crafted investment opportunities</p>
      </div>
      <div className="plans-grid">
        {plans.map(plan => (
          <PlanCard key={plan.id} plan={plan} />
        ))}
      </div>
    </div>
  );
}; 