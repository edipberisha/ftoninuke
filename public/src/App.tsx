/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useEffect } from 'react';
import { 
  Database, 
  Terminal, 
  FileCode, 
  ShieldCheck, 
  RefreshCw, 
  ArrowRight, 
  Layers, 
  CheckCircle2, 
  AlertTriangle,
  Code,
  Github,
  Zap,
  Palette,
  Globe
} from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { GoogleGenAI } from "@google/genai";

const ai = new GoogleGenAI({ apiKey: process.env.GEMINI_API_KEY || '' });

interface ProjectStatus {
  projectName: string;
  legacyVersion: string;
  targetVersion: string;
  compliance: {
    psr4: string;
    pdo: string;
    security: string;
  };
  files: Array<{
    name: string;
    status: string;
    size: string;
    path: string;
  }>;
}

export default function App() {
  const [status, setStatus] = useState<ProjectStatus | null>(null);
  const [activeTab, setActiveTab] = useState<'plan' | 'code' | 'analyze'>('plan');
  const [analysisText, setAnalysisText] = useState('');
  const [legacyCode, setLegacyCode] = useState('// Paste legacy PHP code here...\n$sql = "SELECT * FROM ".$prefix."_users WHERE username=\'$username\'";\n$result = $db->sql_query($sql);');
  const [isAnalyzing, setIsAnalyzing] = useState(false);

  useEffect(() => {
    fetch('/api/project-status')
      .then(res => res.json())
      .then(data => setStatus(data));
  }, []);

  const handleAnalyze = async () => {
    if (!legacyCode.trim()) return;
    setIsAnalyzing(true);
    try {
      const response = await ai.models.generateContent({
        model: "gemini-3-flash-preview",
        contents: `Analyze this legacy PHP-Nuke 8.1 code and suggest a modernization strategy for PHP 8.3+:
        
        \`\`\`php
        ${legacyCode}
        \`\`\`
        
        Focus on PDO migration, security, and PSR-4 namespacing.`,
      });
      
      setAnalysisText(response.text || "No analysis generated.");
      setActiveTab('analyze');
    } catch (err) {
      console.error(err);
      setAnalysisText("AI Analysis failed. Please check your API key and connection.");
    } finally {
      setIsAnalyzing(false);
    }
  };

  if (!status) return (
    <div className="flex h-screen items-center justify-center bg-[#141414]">
      <motion.div 
        animate={{ rotate: 360 }}
        transition={{ repeat: Infinity, duration: 1, ease: 'linear' }}
      >
        <RefreshCw className="text-[#5eff00] w-8 h-8" />
      </motion.div>
    </div>
  );

  return (
    <div className="technical-grid text-[#e4e3e0]">
      {/* Sidebar */}
      <aside className="sidebar flex flex-col p-6 overflow-y-auto">
        <div className="mb-8">
          <div className="col-header mb-1">Architecture</div>
          <h2 className="text-xl font-bold tracking-tight mb-2">FtoniNuke Core</h2>
          <div className="flex items-center gap-2 text-xs opacity-60">
            <span className="status-dot status-active" />
            <span>Modernization Workspace Active</span>
          </div>
        </div>

        <nav className="flex flex-col gap-1 mb-8">
          <div className="col-header mb-2 px-2">Navigation</div>
          {[
            { id: 'plan', label: 'Migration Plan', icon: Layers },
            { id: 'code', label: 'Core Files', icon: FileCode },
            { id: 'analyze', label: 'Legacy Analysis', icon: Terminal },
          ].map(tab => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id as any)}
              className={`flex items-center gap-3 px-3 py-2 rounded transition-colors text-sm ${
                activeTab === tab.id 
                  ? 'bg-[#e4e3e0] text-[#141414]' 
                  : 'hover:bg-white/5'
              }`}
            >
              <tab.icon className="w-4 h-4" />
              {tab.label}
            </button>
          ))}
        </nav>

        <div className="mt-auto">
          <div className="col-header mb-3">Project Specs</div>
          <div className="space-y-3 text-xs">
            <div className="flex justify-between">
              <span className="opacity-50">Legacy:</span>
              <span className="data-value">{status.legacyVersion}</span>
            </div>
            <div className="flex justify-between">
              <span className="opacity-50">Target:</span>
              <span className="data-value">{status.targetVersion}</span>
            </div>
            <div className="border-t border-white/10 pt-3 flex justify-between">
              <span className="opacity-50">Security:</span>
              <span className="text-[#5eff00]">Hardened</span>
            </div>
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <main className="main-content flex flex-col">
        <header className="header backdrop-blur-md bg-[#141414]/80 sticky top-0 z-10">
          <div>
            <h1 className="text-lg font-bold tracking-tight uppercase tracking-widest">{activeTab} Viewer</h1>
            <p className="text-xs opacity-50 font-mono tracking-tighter">ftoni-nuke/migration/{activeTab}.json</p>
          </div>
          <div className="flex gap-3">
            <button className="text-xs border border-white/10 px-3 py-1.5 rounded hover:bg-white/5 flex items-center gap-2">
              <Github className="w-3 h-3" /> Source
            </button>
            <button 
              onClick={handleAnalyze}
              disabled={isAnalyzing}
              className="text-xs bg-[#5eff00] text-[#141414] px-4 py-1.5 rounded font-bold hover:brightness-110 flex items-center gap-2 disabled:opacity-50"
            >
              <RefreshCw className={`w-3 h-3 ${isAnalyzing ? 'animate-spin' : ''}`} /> 
              {isAnalyzing ? 'Analyzing...' : 'Modernize Snippet'}
            </button>
          </div>
        </header>

        <div className="p-8 max-w-5xl mx-auto w-full">
          <AnimatePresence mode="wait">
            {activeTab === 'plan' && (
              <motion.div
                key="plan"
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -10 }}
                className="space-y-12"
              >
                <section>
                  <div className="col-header mb-4">Executive Summary</div>
                  <p className="text-lg leading-relaxed text-white/80">
                    Modernizing a global state architecture from 2009 requires a strategic shift. 
                    We are moving away from <code className="text-[#5eff00] bg-white/5 px-2 py-0.5 rounded">global $db</code> and 
                    raw MySQL queries to a **Dependency Injection** and **PDO-first** ecosystem.
                  </p>
                </section>

                <section className="grid grid-cols-3 gap-6">
                  {[
                    { title: 'Database', desc: 'Full PDO implementation with prepared statements.', icon: Database, color: '#5eff00' },
                    { title: 'Security', desc: 'Session hardening & SQL Injection protection.', icon: ShieldCheck, color: '#ffaa00' },
                    { title: 'PSR-4', desc: 'Namespace-aware autoloading for classes.', icon: Layers, color: '#3b82f6' },
                  ].map((card, i) => (
                    <div key={i} className="terminal-box bg-white/5 border-white/10 p-5 group hover:border-[#5eff00]/30 transition-all">
                      <card.icon className="w-6 h-6 mb-3" style={{ color: card.color }} />
                      <h3 className="font-bold mb-1">{card.title}</h3>
                      <p className="text-xs opacity-60 leading-normal">{card.desc}</p>
                    </div>
                  ))}
                </section>

                <section className="grid grid-cols-2 gap-6">
                   <div className="terminal-box bg-[#5eff00]/5 border-[#5eff00]/20">
                      <ShieldCheck className="w-5 h-5 text-[#5eff00] mb-3" />
                      <h4 className="font-bold mb-2">Auth Module: Modernized</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Legacy MD5 to Bcrypt auto-migration</li>
                        <li>Secure Session storage (Native PHP)</li>
                        <li>Protection against Session Fixation</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-[#3b82f6]/5 border-[#3b82f6]/20">
                      <Database className="w-5 h-5 text-[#3b82f6] mb-3" />
                      <h4 className="font-bold mb-2">User Repository</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Decoupled Data Access Layer</li>
                        <li>Strictly Type-hinted Methods</li>
                        <li>PDO-ready Prepared Statements</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-white/5 border-white/10">
                      <Code className="w-5 h-5 text-purple-400 mb-3" />
                      <h4 className="font-bold mb-2">View & Controller</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Separation of Logic (MVC Pattern)</li>
                        <li>Modern Grid/Flexbox Layout</li>
                        <li>CSRF-protected Post Requests</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-orange-400/5 border-orange-400/20">
                      <RefreshCw className="w-5 h-5 text-orange-400 mb-3" />
                      <h4 className="font-bold mb-2">News & Content</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>SEO-friendly Slug System</li>
                        <li>Legacy mapping (nuke_stories)</li>
                        <li>Service-layer Hit Tracking</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-pink-400/5 border-pink-400/20">
                      <Layers className="w-5 h-5 text-pink-400 mb-3" />
                      <h4 className="font-bold mb-2">Blocks & Widgets</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Standardized Widget Interface</li>
                        <li>File-based Output Caching</li>
                        <li>Dynamic Multi-position Loading</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-blue-400/5 border-blue-400/20">
                      <Terminal className="w-5 h-5 text-blue-400 mb-3" />
                      <h4 className="font-bold mb-2">Layout & Theme</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Centralized Asset Management</li>
                        <li>Responsive Grid-based Layout</li>
                        <li>Conditional Sidebar Logic</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-[#5eff00]/10 border-[#5eff00]/30 shadow-[0_0_20px_rgba(94,255,0,0.1)]">
                      <ShieldCheck className="w-5 h-5 text-[#5eff00] mb-3" />
                      <h4 className="font-bold mb-2 text-[#5eff00]">Admin Cockpit v1.0</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>RBAC Middleware Security</li>
                        <li>Action-based Admin Logging</li>
                        <li>Real-time Health Monitoring</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-cyan-400/5 border-cyan-400/20">
                      <Zap className="w-5 h-5 text-cyan-400 mb-3" />
                      <h4 className="font-bold mb-2">Headless API v1.1</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>JWT Token Authentication</li>
                        <li>Rate-limited REST Endpoints</li>
                        <li>IoT & Mobile Data Gateway</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-indigo-500/5 border-indigo-500/20">
                      <FileCode className="w-5 h-5 text-indigo-400 mb-3" />
                      <h4 className="font-bold mb-2">Media Library</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Drag & Drop Async Uploads</li>
                        <li>Automated Directory Structuring</li>
                        <li>Slugified File Management</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-amber-400/10 border-amber-400/30 shadow-[0_0_20px_rgba(251,191,36,0.1)]">
                      <Palette className="w-5 h-5 text-amber-400 mb-3" />
                      <h4 className="font-bold mb-2 text-amber-400">Theme Engine v2</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>WP-Style "The Loop" Helpers</li>
                        <li>Dynamic Directory Scanning</li>
                        <li>Admin Theme Forge Integration</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-[#00ffcc]/10 border-[#00ffcc]/30 shadow-[0_0_20px_rgba(0,255,204,0.1)]">
                      <Layers className="w-5 h-5 text-[#00ffcc] mb-3" />
                      <h4 className="font-bold mb-2 text-[#00ffcc]">Widget Ecosystem v1.3</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Universal Widget Discovery</li>
                        <li>Dynamic Instance Configs</li>
                        <li>Drag-Drop Layout Architect</li>
                      </ul>
                   </div>
                   <div className="terminal-box bg-[#5eff00]/10 border-[#5eff00]/30 shadow-[0_0_20px_rgba(94,255,0,0.05)] border-dashed col-span-1 lg:col-span-2">
                      <Globe className="w-5 h-5 text-[#5eff00] mb-3" />
                      <h4 className="font-bold mb-2 text-white">Multilingual Discovery v1.4</h4>
                      <ul className="text-[11px] space-y-1 opacity-80 list-disc list-inside">
                        <li>Global Language Selector (Step 0)</li>
                        <li>7 High-Quality Translation Packs</li>
                        <li>Config-Driven Static Locale Persistence</li>
                        <li className="text-[#5eff00] font-bold">Global Ready Architectural Layer</li>
                      </ul>
                   </div>
                </section>

                <section>
                  <div className="col-header mb-4">File Migration Status</div>
                  <div className="border border-white/10 rounded overflow-hidden">
                    {status.files.map((file, i) => (
                      <div key={i} className="data-row hover:bg-white/5 border-b border-white/5 last:border-0 group">
                        <div className="flex items-center justify-center opacity-30 group-hover:opacity-100">
                          <CheckCircle2 className="w-4 h-4 text-[#5eff00]" />
                        </div>
                        <div className="flex flex-col">
                          <span className="text-sm font-medium">{file.name}</span>
                          <span className="text-[10px] opacity-40 font-mono italic">{file.path}</span>
                        </div>
                        <div className="flex flex-col justify-center">
                          <span className="text-[10px] uppercase tracking-widest opacity-50">Status</span>
                          <span className={`text-xs font-bold ${file.status === 'Modern' ? 'text-[#5eff00]' : 'text-white/50'}`}>
                            {file.status}
                          </span>
                        </div>
                        <div className="flex flex-col justify-center text-right">
                          <span className="text-[10px] uppercase tracking-widest opacity-50">Size</span>
                          <span className="text-xs data-value">{file.size}</span>
                        </div>
                      </div>
                    ))}
                  </div>
                </section>
              </motion.div>
            )}

            {activeTab === 'code' && (
              <motion.div
                key="code"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                className="space-y-8"
              >
                <div className="p-6 bg-white/5 rounded-lg border border-white/10">
                  <div className="flex items-center gap-3 mb-4">
                    <div className="p-2 bg-[#5eff00]/20 rounded text-[#5eff00]">
                      <Code className="w-5 h-5" />
                    </div>
                    <div>
                      <h3 className="font-bold">Proposed Core Implementation</h3>
                      <p className="text-xs opacity-50">src/Core/Database.php & Bootstrap.php</p>
                    </div>
                  </div>
                  
                  <div className="grid gap-4">
                    <div className="terminal-box font-mono text-[12px] opacity-80 whitespace-pre overflow-x-auto max-h-[400px]">
                      {`// src/Core/Database.php
class Database {
    private static ?self $instance = null;
    private PDO $connection;

    public function query(string $sql, array $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    // ... complete secure class implementation
}`}
                    </div>
                    <div className="flex justify-end gap-2">
                       <span className="text-[10px] px-2 py-1 bg-white/5 rounded italic opacity-50">Created for FtoniNuke v2.0</span>
                    </div>
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-6">
                   <div className="terminal-box border-[#ffaa00]/30">
                      <AlertTriangle className="w-4 h-4 text-[#ffaa00] mb-2" />
                      <h4 className="text-sm font-bold mb-1">Security Warning</h4>
                      <p className="text-[11px] opacity-60">Global scope $db usage in legacy modules must be wrapped via the Bootstrap Bridge to prevent session leakage.</p>
                   </div>
                   <div className="terminal-box border-[#3b82f6]/30">
                      <Layers className="w-4 h-4 text-[#3b82f6] mb-2" />
                      <h4 className="text-sm font-bold mb-1">Dependency Injection</h4>
                      <p className="text-[11px] opacity-60">Consider using PHP-DI for future services to completely eliminate global variable dependencies.</p>
                   </div>
                </div>
              </motion.div>
            )}

            {activeTab === 'analyze' && (
              <motion.div
                key="analyze"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                className="space-y-6"
              >
                <div className="col-header">Code Sandbox</div>
                <textarea
                  value={legacyCode}
                  onChange={(e) => setLegacyCode(e.target.value)}
                  className="w-full h-[200px] bg-black border border-white/10 rounded p-4 font-mono text-sm highlight-none outline-none focus:border-[#5eff00]/50 transition-colors"
                />
                
                {analysisText && (
                  <div className="space-y-4">
                    <div className="col-header">Modernization Report</div>
                    <div className="terminal-box bg-white/5 border-white/10 p-6 leading-relaxed whitespace-pre-wrap">
                      {analysisText}
                    </div>
                  </div>
                )}
                
                {!analysisText && !isAnalyzing && (
                  <div className="flex flex-col items-center justify-center py-12 opacity-30 border border-dashed border-white/10 rounded">
                    <RefreshCw className="w-12 h-12 mb-4" />
                    <p className="text-sm">Enter legacy code above and click "Modernize Snippet"</p>
                  </div>
                )}

                {isAnalyzing && (
                  <div className="flex flex-col items-center justify-center py-12 text-[#5eff00]">
                    <RefreshCw className="w-12 h-12 mb-4 animate-spin" />
                    <p className="text-sm font-bold tracking-widest uppercase">Consulting Gemini AI...</p>
                  </div>
                )}
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </main>
    </div>
  );
}
