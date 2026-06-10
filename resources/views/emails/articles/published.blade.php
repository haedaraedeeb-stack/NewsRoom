<x-mail::message>
# Hello {{ $writerName }},

Great news! Your article **"{{ $articleTitle }}"** has been successfully published on NewsRoom.

You can view your published article by clicking the button below:

<x-mail::button :url="$articleUrl">
    View Article
</x-mail::button>

Keep up the great work!

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
