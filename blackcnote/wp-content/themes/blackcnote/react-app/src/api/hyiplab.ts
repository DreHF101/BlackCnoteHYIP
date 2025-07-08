// API integration for Hyiplab plugin
export interface Plan {
  id: number;
  name: string;
  description: string;
  min_amount: number;
  max_amount: number;
  interest_rate: number;
  term_days: number;
  status: number;
  created_at: string;
  updated_at: string;
}

export interface User {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  balance: number;
}

export interface Investment {
  id: number;
  user_id: number;
  plan_id: number;
  amount: number;
  status: string;
  created_at: string;
  plan?: Plan;
}

export interface Transaction {
  id: number;
  user_id: number;
  amount: number;
  charge: number;
  final_amount: number;
  method: string;
  trx: string;
  type: string;
  remark: string;
  created_at: string;
}

export interface PlatformStats {
  total_users: number;
  total_investments: number;
  total_transactions: number;
  active_plans: number;
}

export interface AppSettings {
  site_name: string;
  currency_symbol: string;
  // Add other settings properties here
}

export interface CalculationResult {
  daily: string;
  total: string;
}

// Extend the Window interface to include our custom data
declare global {
  interface Window {
    blackcnoteData?: {
      nonce?: string;
      // other properties can be added here
    };
  }
}

class HyiplabAPI {
  private baseUrl: string;
  private nonce: string;

  constructor() {
    this.baseUrl = '/wp-json/blackcnote/v1';
    this.nonce = window.blackcnoteData?.nonce || '';
  }

  private async request(endpoint: string, options: RequestInit = {}): Promise<Response> {
    const url = `${this.baseUrl}${endpoint}`;
    const config: RequestInit = {
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': this.nonce,
        ...options.headers,
      },
      ...options,
    };

    const response = await fetch(url, config);
    
    if (!response.ok) {
      throw new Error(`API Error: ${response.status} ${response.statusText}`);
    }
    
    return response;
  }

  // Plans
  async getPlans(): Promise<Plan[]> {
    const response = await this.request('/plans');
    return response.json();
  }

  // User Data
  async getUserData(): Promise<User> {
    const response = await this.request('/user');
    return response.json();
  }

  async getUserInvestments(): Promise<Investment[]> {
    const response = await this.request('/investments');
    return response.json();
  }

  async getUserTransactions(): Promise<Transaction[]> {
    const response = await this.request('/transactions');
    return response.json();
  }

  // Platform Stats
  async getPlatformStats(): Promise<PlatformStats> {
    const response = await this.request('/stats');
    return response.json();
  }

  // Actions
  async createInvestment(data: { plan_id: number; amount: number }): Promise<{ message: string }> {
    const response = await this.request('/invest', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    return response.json();
  }

  async processWithdrawal(data: { amount: number; method: string }): Promise<{ message: string }> {
    const response = await this.request('/withdraw', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    return response.json();
  }

  async processDeposit(data: { amount: number; gateway: string }): Promise<{ message: string }> {
    const response = await this.request('/deposit', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    return response.json();
  }

  // Profile
  async updateProfile(data: { firstName?: string; lastName?: string; email?: string }): Promise<{ message: string }> {
    const response = await this.request('/profile', {
      method: 'PUT',
      body: JSON.stringify(data),
    });
    return response.json();
  }

  // Calculator
  async calculateReturns(amount: number, planId: number): Promise<CalculationResult> {
    const response = await this.request(`/calculate?amount=${amount}&planId=${planId}`);
    return response.json();
  }

  // App Settings
  async getAppSettings(): Promise<AppSettings> {
    const response = await this.request('/settings');
    return response.json();
  }
}

export const hyiplabAPI = new HyiplabAPI(); 