<script setup>
import { Link, Head } from '@inertiajs/vue3';
</script>
<template>
    <Head :title="__('dashboard')['welcome']" />
    <div class="min-h-screen bg-[#fff]">
      <!-- Header -->
      <header class="flex justify-between items-center p-4 bg-[#162a3f]">
        <div class="flex items-center">
          <img src="../../images/eveky.png" alt="Eveky Logo" class="h-12" />
        </div>
        <nav>
          <Link
            href="/login"
            class="px-6 py-2 rounded-full bg-[#f29260] text-white hover:bg-[#ef5461] transition-colors"
          >
            Login
          </Link>
        </nav>
      </header>

      <!-- Main Content -->
      <main class="container mx-auto px-4 py-12 text-center">
        <!-- Welcome Section -->
        <section class="mb-16">
          <h1 class="text-4xl font-bold text-[#162a3f] mb-4">
            Välkommen till Eveky
          </h1>
        </section>

        <!-- About Section -->
        <section class="max-w-2xl mx-auto">
          <div class="mb-10 bg-[#6accad] p-6 rounded-lg text-left">
            <p class="text-white leading-relaxed">
                Eveky är din ultimata följeslagare för evenemangsplanering, nu med en helt ny mobilupplevelse. Vår kommande mobilapp är designad för att ge dig ännu mer flexibilitet och enkelhet när du planerar och hanterar dina speciella tillfällen.<p class="text-white">Med Eveky kan du:</p>
                <ul class="list-disc pl-6 text-white">
                    <li>Skapa och hantera önskelistor för födelsedagar och andra evenemang.</li>
                    <li>Bjuda in gäster direkt från mobilen och följa upp vilka som kommer.</li>
                    <li>Hålla koll på allt från önskemål till deltagarlistor, oavsett var du är.</li>
                </ul>
            </p>
          </div>

          <div class="mb-10 bg-[#2d1646] p-6 rounded-lg text-left">
            <p class="text-white leading-relaxed">
                Dashboarden för vår mobilapp erbjuder en intuitiv och användarvänlig vy där du enkelt kan:
                <ul class="list-disc pl-6 text-white">
                    <li>Få en snabb överblick av alla dina pågående och kommande evenemang.</li>
                    <li>Hantera gästlistor, kommunicera med gäster och spåra RSVPs i realtid.</li>
                    <li>Organisera dina önskelistor med några få knapptryckningar.</li>
                </ul>
            </p>
          </div>

          <div class="bg-[#ef5461] p-6 rounded-lg text-left">
            <p class="text-white leading-relaxed font-bold">
                Eveky-appen lanseras snart och kommer att revolutionera sättet du planerar och njuter av dina speciella ögonblick. Håll utkik – smidigare evenemangsplanering är bara ett par klick bort!
            </p>
          </div>

        </section>
      </main>
    </div>
  </template>

  <script>
  export default {
    name: 'EvekyLandingPage'
  }
  </script>

  <style scoped>
  /* Add any additional custom styles here */
  </style>
