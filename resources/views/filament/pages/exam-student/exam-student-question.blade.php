<x-filament-panels::page>
    <livewire:CountdownTimer :examStudent="$exam_student">
    <div class="flex gap-4">
        <div class="rounded-lg w-3/3">
            <livewire:ExamStudent.ExamStudentDetailAnswer :examStudent="$exam_student" />
        </div>
        <div class="flex-1 rounded-lg">
            {{ $this->form }}
        </div>
    </div>
</x-filament-panels::page>
