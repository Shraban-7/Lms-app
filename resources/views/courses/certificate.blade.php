<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - {{ $course->title }}</title>
    @vite(['resources/css/app.css'])
    @fonts
    <style>
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .certificate-preview-box {
                box-shadow: none !important;
                margin: 0 !important;
                border-width: 20px !important;
            }
        }
    </style>
</head>

<body class="bg-gray-200 text-gray-900 p-8 flex justify-center items-center min-h-screen">
    <div>
        <div class="no-print max-w-[850px] mx-auto mb-6 flex justify-between items-center">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 font-semibold text-sm rounded-lg hover:-translate-y-0.5 transition-all duration-300 shadow-sm cursor-pointer">
                ← Back to Dashboard
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary text-white font-semibold text-sm rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-300 shadow-md cursor-pointer">
                🖨️ Print Certificate
            </button>
        </div>

        <div class="certificate-preview-box">
            <div class="cert-title">CERTIFICATE OF COMPLETION</div>
            <div class="cert-subtitle">This is proudly presented to</div>

            <div class="cert-recipient">{{ $user->name }}</div>

            <div class="cert-details text-gray-700">
                for successfully meeting all academic requirements and passing all graded assessments for the course
                <br>
                <strong class="text-2xl text-[#141A2D] font-sans font-extrabold block my-4">
                    {{ $course->title }}
                </strong>
                demonstrating competency and professional-grade skill mastery.
            </div>

            <div class="cert-footer">
                <div class="text-left text-xs text-gray-600">
                    <div>Date Issued: <strong class="font-semibold">{{ $certificate->issued_at->format('M d, Y') }}</strong></div>
                    <div class="mt-1">Verification: <code class="font-mono bg-gray-100 border border-gray-200 px-1 py-0.5 rounded text-gray-800">{{ $certificate->verification_code }}</code></div>
                </div>

                <div class="cert-badge-gold">
                    AetherLMS
                </div>

                <div class="cert-sig-block">
                    <div class="font-serif italic text-xl text-[#141A2D] mb-1">
                        {{ $course->instructor->name }}
                    </div>
                    <span class="text-[10px] text-gray-500 uppercase font-sans font-bold tracking-wider">
                        Course Instructor
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
