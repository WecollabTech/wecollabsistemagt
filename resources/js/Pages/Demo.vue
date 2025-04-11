<script setup>
import { ref, onMounted, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/effect-fade";
import { Navigation, Pagination, Autoplay, EffectFade } from "swiper/modules";
import Swal from "sweetalert2";

import Header from "@/Vistas/Header.vue";
import Footer from "@/Vistas/Footer.vue";

// Estado general
const selectedCategory = ref("Todos");
const isSwiperPaused = ref(false);
const swiperInstance = ref(null);
const searchQuery = ref("");

// Tutoriales de muestra para la demo
const demoTutoriales = ref([
    {
        id: 1,
        titulo: "Configuración Inicial de Bitrix24 CRM",
        descripcion: "Aprende a configurar tu CRM en Bitrix24 desde cero.",
        imagen: "https://www.bitrix24.com/images/logo/bitrix24.svg",
        categoria: "CRM",
        duracion: "1.5 horas",
        progreso: 25,
    },
    {
        id: 2,
        titulo: "Automatización de Procesos en Bitrix24",
        descripcion: "Optimiza flujos de trabajo con reglas y triggers.",
        imagen: "https://www.bitrix24.com/images/logo/bitrix24.svg",
        categoria: "Automatización",
        duracion: "2 horas",
        progreso: 50,
    },
]);

// Filtrar tutoriales
const filteredTutoriales = computed(() => {
    let filtered = demoTutoriales.value;
    if (selectedCategory.value !== "Todos") {
        filtered = filtered.filter(
            (tutorial) => tutorial.categoria === selectedCategory.value
        );
    }
    if (searchQuery.value) {
        filtered = filtered.filter((tutorial) =>
            tutorial.titulo
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase())
        );
    }
    return filtered;
});

// Diapositivas del carrusel
const carouselSlides = [
    {
        imagen: "https://www.bitrix24.com/images/content_crm.jpg",
        titulo: "WE COLLAB & BITRIX24 - DEMO",
        subtitulo: "Prueba nuestro sistema sin registrarte.",
        detalle: "Explora una muestra de nuestras capacitaciones en Bitrix24.",
        boton: "Ver Demo",
    },
    {
        imagen: "https://www.bitrix24.com/images/content_automation.jpg",
        titulo: "SOLUCIONES SIMPLES",
        subtitulo: "Optimiza tu trabajo con Bitrix24.",
        detalle: "Descubre cómo gestionamos tareas y ventas de forma efectiva.",
        boton: "Probar Ahora",
    },
];

// Control del autoplay del Swiper
const toggleSwiperAutoplay = (swiper) => {
    isSwiperPaused.value = !isSwiperPaused.value;
    isSwiperPaused.value ? swiper.autoplay.stop() : swiper.autoplay.start();
};

// Acción al intentar acceder a contenido completo
const handleDemoClick = () => {
    Swal.fire({
        title: "¡Regístrate para continuar!",
        text: "Esta es una demo. Regístrate para acceder a todos los tutoriales y funcionalidades.",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Regístrate",
        cancelButtonText: "Seguir en Demo",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/register";
        }
    });
};

// Animaciones con IntersectionObserver
onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(
                        "animate__animated",
                        "animate__fadeInUp"
                    );
                }
            });
        },
        { threshold: 0.2 }
    );

    document.querySelectorAll(".observe").forEach((el) => observer.observe(el));
});
</script>

<template>
    <Head title="Demo - We Collab">
        <meta
            name="description"
            content="Prueba nuestro sistema de gestión de tutoriales para Bitrix24 sin registrarte"
        />
        <meta
            name="keywords"
            content="Bitrix24, demo, tutoriales, We Collab, multiplaforma"
        />
    </Head>
    <div class="bg-gray-50 min-h-screen">
        <Header
            :isLoginPage="false"
            :canLogin="true"
            :canRegister="true"
            :showHeaderBottom="false"
        />

        <!-- Carrusel de Introducción -->
        <main
            class="relative w-full h-[60vh] sm:h-[70vh] md:h-[80vh] lg:h-[90vh] overflow-hidden"
        >
            <Swiper
                :modules="[Navigation, Pagination, Autoplay, EffectFade]"
                :spaceBetween="0"
                :slidesPerView="1"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{ delay: 8000, disableOnInteraction: false }"
                :effect="'fade'"
                :fadeEffect="{ crossFade: true }"
                class="w-full h-full"
                @swiper="(swiper) => (swiperInstance = swiper)"
            >
                <SwiperSlide
                    v-for="(slide, index) in carouselSlides"
                    :key="index"
                >
                    <div
                        class="relative w-full h-full bg-cover bg-center flex items-center justify-center"
                        :style="{ backgroundImage: `url(${slide.imagen})` }"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-black/70 via-blue-900/50 to-blue-700/40"
                        ></div>
                        <div
                            class="relative z-10 text-center text-white max-w-[90%] sm:max-w-3xl lg:max-w-5xl mx-auto px-4"
                        >
                            <img
                                src="/img/wecollab.png"
                                alt="Logo We Collab"
                                class="w-24 sm:w-32 lg:w-40 mx-auto mb-4"
                            />
                            <h1
                                class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-shadow"
                            >
                                {{ slide.titulo }}
                            </h1>
                            <p
                                class="mt-3 text-base sm:text-lg md:text-xl lg:text-2xl text-shadow"
                            >
                                {{ slide.subtitulo }}
                            </p>
                            <p
                                class="mt-2 text-sm sm:text-base md:text-lg text-shadow"
                            >
                                {{ slide.detalle }}
                            </p>
                            <button
                                class="mt-6 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-full font-semibold hover:from-orange-600 hover:to-orange-700 transition-colors"
                                @click="handleDemoClick"
                            >
                                {{ slide.boton }}
                            </button>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
            <button
                @click="toggleSwiperAutoplay(swiperInstance)"
                class="absolute bottom-4 left-4 bg-blue-600/90 p-2 sm:p-3 rounded-full text-white z-20 hover:bg-blue-700 hover:scale-110 transition-transform shadow-lg"
                title="Pausar/Reanudar carrusel"
            >
                <i :class="isSwiperPaused ? 'fas fa-play' : 'fas fa-pause'"></i>
            </button>
        </main>

        <!-- Sección de Tutoriales Demo -->
        <section
            class="py-8 sm:py-12 px-4 sm:px-6 bg-gradient-to-b from-blue-100 to-blue-50 observe"
        >
            <div class="max-w-[95%] sm:max-w-4xl lg:max-w-6xl mx-auto">
                <h2
                    class="text-center text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-6 sm:mb-8"
                >
                    Capacitaciones de Muestra
                </h2>
                <p
                    class="text-center text-sm sm:text-base md:text-lg text-gray-600 mb-6 sm:mb-8"
                >
                    Explora una versión demo de nuestro sistema de gestión de
                    tutoriales.
                </p>

                <!-- Filtros Simplificados -->
                <div
                    class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 items-center mb-6 sm:mb-8"
                >
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar capacitaciones..."
                        class="px-3 py-2 sm:px-4 sm:py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full max-w-[20rem] shadow-sm"
                    />
                    <div
                        class="flex flex-wrap gap-2 sm:gap-4 mt-3 sm:mt-0 justify-center"
                    >
                        <button
                            v-for="category in [
                                'Todos',
                                'CRM',
                                'Automatización',
                            ]"
                            :key="category"
                            @click="selectedCategory = category"
                            :class="[
                                'px-3 py-1 sm:px-4 sm:py-2 rounded-full font-semibold text-xs sm:text-sm',
                                selectedCategory === category
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-100',
                            ]"
                        >
                            {{ category }}
                        </button>
                    </div>
                </div>

                <!-- Lista de Tutoriales -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
                >
                    <div
                        v-for="tutorial in filteredTutoriales"
                        :key="tutorial.id"
                        class="relative bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg transform hover:-translate-y-2 transition-all duration-300 group"
                        @click="handleDemoClick"
                    >
                        <img
                            :src="tutorial.imagen"
                            :alt="`Imagen de ${tutorial.titulo}`"
                            class="w-full h-32 sm:h-40 lg:h-48 object-contain rounded-t-2xl bg-white p-3 sm:p-4"
                            loading="lazy"
                        />
                        <div class="p-4 sm:p-6">
                            <h3
                                class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800"
                            >
                                {{ tutorial.titulo }}
                            </h3>
                            <p
                                class="mt-2 text-gray-600 text-xs sm:text-sm lg:text-base line-clamp-2"
                            >
                                {{ tutorial.descripcion }}
                            </p>
                            <div
                                class="mt-3 sm:mt-4 text-xs sm:text-sm text-gray-500 space-y-1"
                            >
                                <p>
                                    <strong>Categoría:</strong>
                                    {{ tutorial.categoria }}
                                </p>
                                <p>
                                    <strong>Duración:</strong>
                                    {{ tutorial.duracion }}
                                </p>
                            </div>
                            <div
                                class="mt-3 sm:mt-4 h-2 bg-gray-200 rounded-full overflow-hidden"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000"
                                    :style="{ width: `${tutorial.progreso}%` }"
                                ></div>
                            </div>
                            <p
                                class="mt-3 sm:mt-4 text-xs text-gray-500 italic"
                            >
                                *Demo: Regístrate para ver el contenido completo
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botón de Registro -->
                <div class="text-center mt-8 sm:mt-12">
                    <p class="text-gray-600 text-sm sm:text-base mb-4">
                        ¿Te gusta lo que ves? ¡Regístrate para desbloquear todo
                        el contenido!
                    </p>
                    <button
                        class="bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-800 px-6 py-2 sm:px-8 sm:py-3 rounded-full font-semibold hover:from-yellow-500 hover:to-orange-600 transition-colors"
                        @click="window.location.href = '/register'"
                    >
                        Regístrate Ahora
                    </button>
                </div>
            </div>
        </section>

        <Footer class="mt-8 sm:mt-12" />
    </div>
</template>

<style scoped>
/* Estilos generales */
.bg-gray-50 {
    font-family: "Inter", sans-serif;
    background: linear-gradient(to bottom, #f3f4f6, #e5e7eb);
}

/* Animaciones para tarjetas */
.bg-white,
.bg-gradient-to-br {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.bg-white:hover,
.bg-gradient-to-br:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

/* Sombra para textos */
.text-shadow {
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
}

/* Animaciones */
.animate__animated {
    animation-duration: 0.6s;
}

.animate__fadeInDown {
    animation-name: fadeInDown;
}

.animate__fadeInUp {
    animation-name: fadeInUp;
}

.animate__delay-1s {
    animation-delay: 0.6s;
}

.animate__delay-2s {
    animation-delay: 1.2s;
}

.animate__delay-3s {
    animation-delay: 1.8s;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes gradient-slow {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient-slow {
    background-size: 200% 200%;
    animation: gradient-slow 15s ease infinite;
}

/* Línea de texto truncada */
.line-clamp-2 {
    display: -webkit-box;

    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsividad */
@media (max-width: 640px) {
    .h-60vh {
        height: 50vh;
    }
    .text-3xl {
        font-size: 1.75rem;
    }
    .text-xl {
        font-size: 1.125rem;
    }
    .text-base {
        font-size: 0.875rem;
    }
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
    .swiper-slide {
        padding: 0 8px;
    }
    .px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    .py-8 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    .h-70vh {
        height: 65vh;
    }
    .text-4xl {
        font-size: 2.25rem;
    }
    .text-2xl {
        font-size: 1.5rem;
    }
    .grid-cols-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1025px) {
    .h-90vh {
        height: 80vh;
    }
}
</style>
