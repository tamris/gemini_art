import fs from 'fs';
import { icons } from 'lucide';

const wantedIcons = [
  'LayoutDashboard', 'ClipboardList', 'PenTool', 'ShoppingCart', 'Printer',
  'Layers', 'Wallet', 'Cloud', 'Archive', 'Building2', 'User', 'LogOut',
  'CheckCircle2', 'Clock', 'AlertCircle', 'AlertTriangle', 'ArrowRight', 'ArrowLeft',
  'Plus', 'Minus', 'X', 'Download', 'UploadCloud', 'Search', 'Trash2', 'Receipt',
  'TrendingUp', 'TrendingDown', 'RefreshCw', 'SlidersHorizontal', 'Calendar',
  'Sparkles', 'FileImage', 'FileText', 'Tag', 'Scissors', 'Copy', 'Eye', 'Lock',
  'ShieldCheck', 'HelpCircle', 'ChevronRight', 'ChevronLeft', 'Check', 'CreditCard',
  'Banknote', 'Coins', 'Percent', 'Store', 'Palette', 'FileCheck', 'Share2', 'History',
  'Folder', 'FolderOpen', 'File', 'HardDrive', 'Cpu', 'CheckCheck', 'ExternalLink',
  'Phone', 'Mail', 'MapPin', 'FileSpreadsheet', 'Flame', 'CircleDot',
  'Info', 'Package', 'Boxes', 'DollarSign', 'Settings', 'Activity', 'Menu', 'Hash',
  'QrCode', 'FileCode', 'Sparkle', 'CircleAlert', 'CircleCheck'
];

function astToSvgInner(ast) {
  return ast.map(([tag, attrs]) => {
    const attrEntries = Object.entries(attrs).map(([k, v]) => `${k}="${v}"`).join(' ');
    return `<${tag} ${attrEntries} />`;
  }).join('');
}

const map = {};
for (const name of wantedIcons) {
  if (icons[name]) {
    const kebab = name.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
    map[kebab] = astToSvgInner(icons[name]);
  } else {
    console.log('Not found in lucide:', name);
  }
}

// Generate Blade component
const bladeContent = `@props([
    'name' => 'circle-dot',
    'class' => 'w-4 h-4',
    'strokeWidth' => '2'
])

@php
$icons = ${JSON.stringify(map, null, 4)};

$svgContent = $icons[$name] ?? ($icons['circle-dot'] ?? '');
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} 
     xmlns="http://www.w3.org/2000/svg" 
     viewBox="0 0 24 24" 
     fill="none" 
     stroke="currentColor" 
     stroke-width="{{ $strokeWidth }}" 
     stroke-linecap="round" 
     stroke-linejoin="round"
     data-lucide="{{ $name }}">
    {!! $svgContent !!}
</svg>
`;

fs.mkdirSync('./resources/views/components', { recursive: true });
fs.writeFileSync('./resources/views/components/icon.blade.php', bladeContent, 'utf-8');
console.log('Successfully written components/icon.blade.php with', Object.keys(map).length, 'icons');
