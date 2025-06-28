# BlackCnote Code Structure Guide

## 🏗️ Project Architecture Overview

BlackCnote uses a **hybrid architecture** combining React for the frontend UI and WordPress for the backend CMS. This gives you the best of both worlds: modern React development with WordPress's powerful content management.

## 📁 React App Structure (`react-app/`)

### Core Directories

```
react-app/
├── src/
│   ├── components/          # Reusable UI components
│   ├── pages/              # Page-level components
│   ├── api/                # API integration layer
│   ├── config/             # Configuration files
│   ├── types/              # TypeScript type definitions
│   └── test/               # Test files
├── public/                 # Static assets
├── blackcnote/dist/        # Build output (goes to WordPress)
└── package.json           # Dependencies and scripts
```

### Key Components

#### `src/components/`
- **Header.tsx** - Main navigation header
- **Footer.tsx** - Site footer
- **Dashboard/** - Dashboard-specific components
- **Plans/** - Investment plan components
- **WelcomeBanner.tsx** - New development banner (example)

#### `src/pages/`
- **HomePage.tsx** - Landing page
- **Dashboard.tsx** - User dashboard
- **InvestmentPlans.tsx** - Investment plans page
- **Calculator.tsx** - Investment calculator
- **Profile.tsx** - User profile
- **Transactions.tsx** - Transaction history
- **Contact.tsx** - Contact page
- **About.tsx** - About page

### Development Workflow

1. **Edit React Components**: Modify files in `src/components/` or `src/pages/`
2. **Hot Reload**: Changes appear instantly at http://localhost:5174
3. **Build for Production**: `npm run build` creates files in `blackcnote/dist/`
4. **WordPress Integration**: Built files are served by WordPress theme

## 🎨 WordPress Theme Structure (`blackcnote/wp-content/themes/blackcnote/`)

### Core Files

```
blackcnote/wp-content/themes/blackcnote/
├── functions.php           # Theme functions and setup
├── header.php             # WordPress header template
├── footer.php             # WordPress footer template
├── front-page.php         # Homepage template
├── index.php              # Main template file
├── style.css              # Theme stylesheet
├── assets/                # Theme assets
├── dist/                  # Built React assets (from react-app)
├── inc/                   # PHP includes
├── template-parts/        # Template parts
├── page-templates/        # Custom page templates
└── widgets/               # Custom widgets
```

### Key Integration Points

#### `functions.php`
- Enqueues React built assets
- Registers custom post types
- Handles API endpoints
- Manages theme settings

#### `front-page.php`
- Main homepage template
- Integrates with React components
- Handles WordPress content

#### `dist/` Directory
- Contains built React assets
- Automatically updated when you run `npm run build`
- Served by WordPress theme

## 🔧 Development Workflow

### 1. React Development (Local)

```bash
# Start React development server
cd react-app
npm run dev

# Edit components in src/
# See changes instantly at http://localhost:5174
```

**What to edit:**
- `src/components/` - Reusable UI components
- `src/pages/` - Page-level components
- `src/api/` - API integration
- `src/config/` - Configuration

### 2. WordPress Development

```bash
# Edit WordPress theme files
# Changes reflect at http://localhost:8888
```

**What to edit:**
- `blackcnote/wp-content/themes/blackcnote/functions.php` - Theme functionality
- `blackcnote/wp-content/themes/blackcnote/front-page.php` - Homepage template
- `blackcnote/wp-content/themes/blackcnote/template-parts/` - Template parts
- `blackcnote/wp-content/themes/blackcnote/page-templates/` - Custom templates

### 3. Integration & Build

```bash
# Build React for production
cd react-app
npm run build

# Built files go to blackcnote/dist/
# WordPress serves these files
```

## 🎯 Key Development Areas

### Frontend (React)
1. **UI Components** - Create reusable components in `src/components/`
2. **Pages** - Build page layouts in `src/pages/`
3. **Styling** - Use CSS-in-JS or Tailwind CSS
4. **State Management** - React hooks and context
5. **API Integration** - Connect to WordPress backend

### Backend (WordPress)
1. **Theme Functions** - Custom functionality in `functions.php`
2. **Templates** - WordPress template hierarchy
3. **Custom Post Types** - Investment plans, testimonials, etc.
4. **API Endpoints** - REST API for React integration
5. **Database** - WordPress database management

### Integration
1. **Asset Loading** - WordPress enqueues React built files
2. **Data Flow** - React fetches data from WordPress API
3. **Routing** - WordPress handles routing, React handles UI
4. **Authentication** - WordPress user system with React UI

## 🚀 Quick Development Tips

### React Development
- **Hot Reload**: Changes appear instantly at http://localhost:5174
- **Component Structure**: Keep components small and focused
- **TypeScript**: Use TypeScript for better type safety
- **Styling**: Use CSS-in-JS or Tailwind for consistent styling

### WordPress Development
- **Template Hierarchy**: Understand WordPress template system
- **Hooks & Filters**: Use WordPress hooks for extensibility
- **Custom Fields**: Use ACF or similar for custom data
- **Performance**: Optimize database queries and caching

### Integration Best Practices
- **API Design**: Design clean REST API endpoints
- **Error Handling**: Handle API errors gracefully
- **Loading States**: Show loading states during API calls
- **Caching**: Implement appropriate caching strategies

## 📚 Next Steps

1. **Explore Components**: Look at existing components in `src/components/`
2. **Study Pages**: Understand page structure in `src/pages/`
3. **Review WordPress**: Check theme files in `blackcnote/wp-content/themes/blackcnote/`
4. **Build Something**: Create a new component or modify existing ones
5. **Test Integration**: Build and test the WordPress integration

## 🔍 File Locations for Common Tasks

| Task | React Location | WordPress Location |
|------|---------------|-------------------|
| Add new page | `src/pages/NewPage.tsx` | `page-templates/new-page.php` |
| Create component | `src/components/NewComponent.tsx` | N/A |
| Modify homepage | `src/pages/HomePage.tsx` | `front-page.php` |
| Add API endpoint | `src/api/newEndpoint.ts` | `functions.php` |
| Custom styling | Component CSS-in-JS | `style.css` |
| Database changes | N/A | `functions.php` (custom post types) |

Happy exploring! 🎨 