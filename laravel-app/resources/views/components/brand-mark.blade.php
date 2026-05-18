@props(['class' => 'h-12 w-12'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="AI Chat logo">
    <defs>
        <linearGradient id="brandLeaf" x1="12" y1="76" x2="84" y2="16" gradientUnits="userSpaceOnUse">
            <stop stop-color="#0891b2"/>
            <stop offset="0.48" stop-color="#22c55e"/>
            <stop offset="1" stop-color="#a3e635"/>
        </linearGradient>
        <linearGradient id="brandPill" x1="40" y1="32" x2="58" y2="67" gradientUnits="userSpaceOnUse">
            <stop stop-color="#67e8f9"/>
            <stop offset="1" stop-color="#0e7490"/>
        </linearGradient>
    </defs>
    <path d="M30 18h-7a9 9 0 0 0-9 9v42a9 9 0 0 0 9 9h42a9 9 0 0 0 9-9v-7" stroke="url(#brandLeaf)" stroke-width="6" stroke-linecap="round"/>
    <path d="M66 18h7a9 9 0 0 1 9 9v30" stroke="url(#brandLeaf)" stroke-width="6" stroke-linecap="round"/>
    <path d="M48 79C39 60 24 58 12 40c18 1 29 9 36 27 7-18 18-26 36-27C72 58 57 60 48 79Z" fill="url(#brandLeaf)"/>
    <path d="M29 34c9 2 16 9 19 24" stroke="white" stroke-width="4" stroke-linecap="round" opacity=".9"/>
    <path d="M67 34c-9 2-16 9-19 24" stroke="white" stroke-width="4" stroke-linecap="round" opacity=".9"/>
    <rect x="39" y="30" width="18" height="35" rx="9" fill="url(#brandPill)" stroke="white" stroke-width="3"/>
    <path d="M40 47h17" stroke="white" stroke-width="3" opacity=".8"/>
    <circle cx="44" cy="56" r="2" fill="white"/>
    <circle cx="50" cy="60" r="2" fill="white"/>
    <circle cx="53" cy="54" r="2" fill="white"/>
</svg>
