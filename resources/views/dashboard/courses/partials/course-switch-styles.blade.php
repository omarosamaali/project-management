{{-- Shared switch styles for course create/edit forms --}}
<style>
    .course-switch {
        position: relative;
        display: inline-flex;
        width: 2.75rem;
        height: 1.5rem;
        flex-shrink: 0;
        cursor: pointer;
    }
    .course-switch input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }
    .course-switch-track {
        display: block;
        width: 100%;
        height: 100%;
        border-radius: 9999px;
        background: #cbd5e1;
        border: 1px solid #94a3b8;
        transition: background .2s ease, border-color .2s ease;
        position: relative;
    }
    .course-switch-track::after {
        content: '';
        position: absolute;
        top: 1px;
        /* Keep LTR on/off semantics (left=off, right=on) even on RTL pages */
        left: 1px;
        width: 1.2rem;
        height: 1.2rem;
        border-radius: 9999px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .25);
        transition: left .2s ease;
    }
    .course-switch input:checked + .course-switch-track {
        background: #0b8f7f;
        border-color: #087a6c;
    }
    .course-switch input:checked + .course-switch-track::after {
        left: calc(100% - 1.2rem - 1px);
    }
    .course-switch input:focus-visible + .course-switch-track {
        outline: 2px solid rgba(11, 143, 127, .45);
        outline-offset: 2px;
    }
    .course-switch.is-disabled {
        opacity: .55;
        pointer-events: none;
        cursor: not-allowed;
    }
    .course-switch-field {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        width: 100%;
        min-height: 3.05rem;
        padding: .75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: .5rem;
        background: #fff;
    }
</style>
