<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TTS Translator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <header class="bg-white border-b">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold">TTS Translator</h1>
            <div class="text-sm text-gray-500">Laravel + Free Translation API</div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border relative">
                <h2 class="text-xl font-semibold mb-4">Translate English text</h2>
                <div class="space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">Text in English</span>
                        <textarea id="inputText" rows="5" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Type text in English...">Hello! This is a demo of a text-to-speech translator.</textarea>
                    </label>

                    <div>
                        <span class="text-sm font-medium text-gray-700">Target language(s)</span>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2" id="languageOptions">
                            @foreach ($languages as $lang)
                                <label class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
                                    <input type="checkbox" class="langOption" value="{{ $lang['code'] }}" />
                                    <span>{{ $lang['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Pick one or multiple languages.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Rate</span>
                            <input id="rate" type="range" min="0.5" max="2" step="0.1" value="1" class="w-full" />
                            <div class="text-xs text-gray-500"><span id="rateVal">1</span>x</div>
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Pitch</span>
                            <input id="pitch" type="range" min="0" max="2" step="0.1" value="1" class="w-full" />
                            <div class="text-xs text-gray-500"><span id="pitchVal">1</span></div>
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Browser Voice</span>
                            <select id="voice" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></select>
                            <p class="text-xs text-gray-500">Used as a fallback if server TTS not configured.</p>
                        </label>
                    </div>

                    <div class="flex items-center gap-3">
                        <button id="btnTranslate" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Translate</button>
                        <button id="btnClear" class="inline-flex items-center bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Clear</button>
                    </div>
                </div>
                <div id="loadingOverlay" class="hidden absolute inset-0 rounded-xl bg-white/70 backdrop-blur-sm flex items-center justify-center">
                    <div class="flex items-center gap-2 text-indigo-700">
                        <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span class="font-medium">Working...</span>
                    </div>
                </div>
            </div>

            <div id="results" class="space-y-4"></div>
        </section>

        <aside class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm p-6 border">
                <h3 class="text-lg font-semibold mb-3">History</h3>
                <div class="space-y-3">
                    @forelse ($history as $item)
                        <div class="border rounded-lg p-3">
                            <div class="text-xs text-gray-500">{{ strtoupper($item->source_lang) }} ➜ {{ strtoupper($item->target_lang) }}</div>
                            <div class="text-sm mt-1 line-clamp-3">{{ $item->translated_text }}</div>
                            <div class="mt-2">
                                <button type="button" class="historyPlay inline-flex items-center text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm" data-text="{{ e($item->translated_text) }}" data-lang="{{ $item->target_lang }}">Play (Browser)</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No history yet.</p>
                    @endforelse
                </div>
                <div class="mt-3">{{ $history->links() }}</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border text-sm text-gray-600">
                <p><strong>Tip:</strong> This app uses your browser's built-in speech synthesis to play audio. Select a voice and press Play.</p>
            </div>
        </aside>
    </main>

    <template id="resultItemTmpl">
        <div class="bg-white rounded-xl shadow-sm p-4 border">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-xs text-gray-500">Translated (<span class="langLabel"></span>)</div>
                    <div class="translated font-medium mt-1"></div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" class="btnPlayBrowser inline-flex items-center text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm">Play</button>
                </div>
            </div>
        </div>
    </template>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        const rateEl = document.getElementById('rate');
        const pitchEl = document.getElementById('pitch');
        const rateVal = document.getElementById('rateVal');
        const pitchVal = document.getElementById('pitchVal');
        const voiceSel = document.getElementById('voice');
        const results = document.getElementById('results');
        const btnTranslate = document.getElementById('btnTranslate');
        const btnClear = document.getElementById('btnClear');
        const inputText = document.getElementById('inputText');
        const loadingOverlay = document.getElementById('loadingOverlay');

        function setDisabled(disabled) {
            btnTranslate.disabled = disabled;
            btnClear.disabled = disabled;
            inputText.disabled = disabled;
            voiceSel.disabled = disabled;
            document.querySelectorAll('.langOption').forEach(cb => cb.disabled = disabled);
        }

        function showLoading(show) {
            if (!loadingOverlay) return;
            loadingOverlay.classList.toggle('hidden', !show);
        }

        rateEl.addEventListener('input', () => rateVal.textContent = rateEl.value);
        pitchEl.addEventListener('input', () => pitchVal.textContent = pitchEl.value);

        // Load browser voices
        let voices = [];
        function loadVoices() {
            voices = window.speechSynthesis?.getVoices?.() || [];
            voiceSel.innerHTML = '';
            voices.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.name;
                opt.textContent = `${v.name} (${v.lang})`;
                voiceSel.appendChild(opt);
            });
        }
        if ('speechSynthesis' in window) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
            loadVoices();
        }

        btnClear.addEventListener('click', () => {
            showLoading(true);
            setDisabled(true);
            inputText.value = '';
            results.innerHTML = '';
            document.querySelectorAll('.langOption').forEach(cb => cb.checked = false);
            // Small delay so the user sees the feedback
            setTimeout(() => {
                showLoading(false);
                setDisabled(false);
            }, 150);
        });

        btnTranslate.addEventListener('click', async () => {
            const text = document.getElementById('inputText').value.trim();
            const targets = Array.from(document.querySelectorAll('.langOption:checked')).map(el => el.value);
            if (!text) return alert('Please enter some text');
            if (targets.length === 0) return alert('Please select at least one language');

            showLoading(true);
            setDisabled(true);
            try {
                const res = await fetch('{{ route('translate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ text, targets })
                });

                if (!res.ok) {
                    const msg = await res.text();
                    alert('Translate failed: ' + msg);
                    return;
                }
                const data = await res.json();
                renderResults(data.items);
            } catch (e) {
                alert('Translate error. Please try again.');
            } finally {
                showLoading(false);
                setDisabled(false);
            }
        });

        function renderResults(items) {
            results.innerHTML = '';
            for (const it of items) {
                const node = document.getElementById('resultItemTmpl').content.cloneNode(true);
                node.querySelector('.langLabel').textContent = it.target.toUpperCase();
                node.querySelector('.translated').textContent = it.translated;
                const btnBrowser = node.querySelector('.btnPlayBrowser');

                btnBrowser.addEventListener('click', () => playBrowser(it.translated, it.target));

                results.appendChild(node);
            }
        }

        function playBrowser(text, lang) {
            if (!('speechSynthesis' in window)) {
                alert('Browser speech synthesis not supported');
                return;
            }

            const langMap = {
                en: 'en-US',
                es: 'es-ES',
                fr: 'fr-FR',
                de: 'de-DE',
                hi: 'hi-IN',
                ar: 'ar-SA',
            };

            const desired = (langMap[lang] || lang || 'en-US');
            const fallbacks = [desired, (navigator.language || 'en-US'), 'en-US'];

            const doSpeak = () => {
                try {
                    const utter = new SpeechSynthesisUtterance(text);
                    utter.lang = fallbacks[0];
                    utter.rate = parseFloat(rateEl.value || '1');
                    utter.pitch = parseFloat(pitchEl.value || '1');

                    let selected = null;
                    const voiceName = voiceSel.value;
                    if (voiceName) {
                        selected = voices.find(v => v.name === voiceName) || null;
                    }
                    if (!selected) {
                        // Pick first voice whose lang starts with requested code
                        selected = voices.find(v => v.lang && (v.lang.toLowerCase().startsWith((fallbacks[0] || '').toLowerCase()) || v.lang.toLowerCase().startsWith((lang || '').toLowerCase())) ) || null;
                    }
                    if (selected) {
                        utter.voice = selected;
                        // align utter.lang to voice if mismatch
                        if (selected.lang) utter.lang = selected.lang;
                    } else {
                        // If no matching voice, try a broader fallback language
                        utter.lang = fallbacks.find(code => !!code) || 'en-US';
                    }

                    utter.onerror = (ev) => {
                        console.error('Speech error', ev.error || ev);
                        alert('Browser speech failed: ' + (ev.error || 'unknown error'));
                    };

                    window.speechSynthesis.cancel();
                    // Some browsers may be paused; resume before speaking
                    if (window.speechSynthesis.paused) {
                        window.speechSynthesis.resume();
                    }
                    window.speechSynthesis.speak(utter);
                } catch (err) {
                    console.error('Speech exception', err);
                    alert('Browser speech could not start.');
                }
            };

            if (!voices || voices.length === 0) {
                // Wait for voices to load
                const once = () => {
                    window.speechSynthesis.onvoiceschanged = null;
                    voices = window.speechSynthesis.getVoices() || [];
                    doSpeak();
                };
                if (window.speechSynthesis.onvoiceschanged !== null) {
                    window.speechSynthesis.onvoiceschanged = once;
                }
                // Also try after a short delay as some browsers don't fire the event reliably
                setTimeout(() => {
                    if (!voices || voices.length === 0) {
                        voices = window.speechSynthesis.getVoices() || voices;
                    }
                    doSpeak();
                }, 300);
            } else {
                doSpeak();
            }
        }

        // expose playBrowser for history buttons
        window.playBrowser = playBrowser;

        // Delegate click for history play buttons to avoid inline handlers and escaping issues
        document.addEventListener('click', (ev) => {
            const btn = ev.target.closest('.historyPlay');
            if (!btn) return;
            const t = btn.dataset.text || '';
            const l = btn.dataset.lang || 'en';
            playBrowser(t, l);
        });
    </script>
</body>
</html>
