// WordPress API integration for BlackCnote React app

export interface WPContent {
  id: number;
  title: string;
  content: string;
  excerpt: string;
  slug: string;
  date: string;
  modified: string;
  // Investment plan fields
  return_rate?: number;
  min_investment?: number;
  max_investment?: number;
  duration?: number;
  features?: string | string[];
}

// Get the correct API base URL
const getApiBaseUrl = (): string => {
  // Check for WordPress injected settings first
  if (typeof window !== 'undefined' && (window as any).blackCnoteApiSettings?.apiUrl) {
    return (window as any).blackCnoteApiSettings.apiUrl;
  }
  
  // Fallback to current origin with BlackCnote API
  return `${window.location.origin}/wp-json/blackcnote/v1`;
};

// Get nonce for authenticated requests
const getNonce = (): string => {
  if (typeof window !== 'undefined' && (window as any).blackCnoteApiSettings?.nonce) {
    return (window as any).blackCnoteApiSettings.nonce;
  }
  return '';
};

// Common fetch wrapper with error handling
const apiFetch = async (endpoint: string, options: RequestInit = {}): Promise<Response> => {
  const apiUrl = getApiBaseUrl();
  const nonce = getNonce();
  
  const defaultHeaders = {
    'Content-Type': 'application/json',
    ...(nonce && { 'X-WP-Nonce': nonce }),
  };

  const response = await fetch(`${apiUrl}/${endpoint.replace(/^\/+/, '')}`, {
    ...options,
    headers: {
      ...defaultHeaders,
      ...options.headers,
    },
  });

  if (!response.ok) {
    const errorText = await response.text();
    console.error(`API Error (${response.status}):`, errorText);
    throw new Error(`API request failed: ${response.status} ${response.statusText}`);
  }

  return response;
};

export async function fetchHomepage(): Promise<WPContent> {
  const res = await apiFetch('homepage');
  return res.json();
}

export async function fetchPlans(): Promise<WPContent[]> {
  const res = await apiFetch('plans');
  return res.json();
}

export async function fetchContent(id: number): Promise<WPContent> {
  const res = await apiFetch(`content/${id}`);
  return res.json();
}

export async function fetchSettings(): Promise<{ live_editing_enabled: boolean; react_integration_enabled: boolean }> {
  const res = await apiFetch('settings');
  return res.json();
}

export async function fetchStats(): Promise<{ totalUsers: number; totalInvested: number; totalPaid: number; activeInvestments: number }> {
  const res = await apiFetch('stats');
  return res.json();
} 