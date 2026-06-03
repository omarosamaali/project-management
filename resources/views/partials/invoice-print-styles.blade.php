<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
        }

        nav,
        aside,
        #drawer-navigation,
        .no-print,
        footer {
            display: none !important;
        }

        main {
            padding: 0 !important;
            margin: 0 !important;
            margin-right: 0 !important;
            height: auto !important;
        }

        section {
            padding: 0 !important;
            margin: 0 !important;
        }

        .mx-auto.max-w-4xl,
        .max-w-4xl {
            max-width: 100% !important;
            margin: 0 !important;
        }

        .print-container {
            position: static !important;
            left: auto !important;
            top: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            box-shadow: none !important;
            overflow: visible !important;
            visibility: visible !important;
            page-break-inside: auto !important;
        }

        .print-container * {
            visibility: visible !important;
        }

        /* الهيدر: عربي | شعار | إنجليزي — كما على الشاشة */
        .invoice-header {
            display: grid !important;
            grid-template-columns: 1fr auto 1fr !important;
            grid-template-rows: auto !important;
            align-items: center !important;
            gap: 16px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .invoice-header__ar {
            grid-column: 1 !important;
            grid-row: 1 !important;
            text-align: right !important;
        }

        .invoice-header__logo {
            grid-column: 2 !important;
            grid-row: 1 !important;
            justify-self: center !important;
            margin: 0 auto !important;
        }

        .invoice-header__en {
            grid-column: 3 !important;
            grid-row: 1 !important;
            text-align: left !important;
        }

        .invoice-two-cols {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 2rem !important;
        }

        .invoice-footer-cols {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 1.5rem !important;
        }

        .overflow-x-auto {
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            table-layout: fixed !important;
        }

        td .flex,
        th .flex {
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        tr {
            page-break-inside: avoid;
        }
    }
</style>
