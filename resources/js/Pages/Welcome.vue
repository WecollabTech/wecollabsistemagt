<script setup>
import { ref, onMounted, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/effect-fade";
import { Navigation, Pagination, Autoplay, EffectFade } from "swiper/modules";
import Swal from "sweetalert2";

import Header from "@/Vistas/Header.vue";
import Footer from "@/Vistas/Footer.vue";
import Prueba from "./Tutoriales/Prueba.vue";

// Estado general
const isLoggedIn = ref(false); // Simula el estado de sesión (ajústalo según tu autenticación)
const selectedCategory = ref("Todos");
const isSwiperPaused = ref(false);
const swiperInstance = ref(null);
const videoRef = ref(null);
const isVideoMuted = ref(true);
const isVideoPlaying = ref(true);
const searchQuery = ref("");
const favorites = ref(JSON.parse(localStorage.getItem("favorites")) || []);

// Lista de tutoriales destacados
const tutoriales = ref([
    {
        id: 1,
        titulo: "Configuración Inicial de Bitrix24 CRM",
        descripcion: "Aprende a configurar tu CRM en Bitrix24 desde cero.",
        imagen: "https://www.bitrix24.mx/upload/optimizer/converted/images/content_es/screens/main/solutions_slider/solutions_slider-2.jpg.webp?1739782993159",
        link: "/bitrix24-crm-setup",
        categoria: "CRM",
        duracion: "1.5 horas",
        esNuevo: true,
        esPopular: true,
        progreso: 25,
    },
    {
        id: 2,
        titulo: "Automatización de Procesos en Bitrix24",
        descripcion: "Optimiza flujos de trabajo con reglas y triggers.",
        imagen: "https://www.bitrix24.mx/upload/optimizer/converted/images/content_en/self-hosted/index-box_1.1600w.jpg.webp?1739782993159",
        link: "/bitrix24-automation",
        categoria: "Automatización",
        duracion: "2 horas",
        esNuevo: false,
        esPopular: true,
        progreso: 70,
    },
    {
        id: 3,
        titulo: "Diseño de Formularios en Bitrix24",
        descripcion: "Crea formularios personalizados para captar leads.",
        imagen: "https://helpdesk.bitrix24.es/upload/medialibrary/d7a/giy13gf0js414ulpgl7fncujriocn2bd/Formulario%20de%20CRM.jpg",
        link: "/bitrix24-forms",
        categoria: "CRM",
        duracion: "1 hora",
        esNuevo: true,
        esPopular: false,
        progreso: 10,
    },
]);

// Filtrar tutoriales
const filteredTutoriales = computed(() => {
    let filtered = tutoriales.value;
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
        imagen: "https://images.unsplash.com/photo-1551288049-b5f3c2e9b23a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80",
        titulo: "WE COLLAB & BITRIX24",
        subtitulo: "Tu espacio de trabajo ideal con tecnología avanzada.",
        detalle:
            "Implementa Bitrix24 de manera sencilla y rápida para maximizar tu ROI.",
        boton: "Descubre Cómo",
        link: "/contact",
    },
    {
        imagen: "https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80",
        titulo: "NUESTRA MISIÓN",
        subtitulo: "Sistemas tecnológicos efectivos para tu organización.",
        detalle:
            "Optimiza comunicación, tareas y ventas con herramientas modernas.",
        boton: "Conoce Más",
        link: "/about",
    },
    {
        imagen: "https://images.unsplash.com/photo-1531973576160-3dcee7a51a9e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80",
        titulo: "SOLUCIONES A MEDIDA",
        subtitulo: "Transformamos problemas en soluciones tecnológicas.",
        detalle: "Experiencia en múltiples industrias con Bitrix24.",
        boton: "Contáctanos",
        link: "/contact",
    },
    {
        imagen: "https://images.unsplash.com/photo-1517430816045-df4b7de11d41?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80",
        titulo: "EXPERIENCIA POR DISEÑO",
        subtitulo: "Diseñamos experiencias tecnológicas excepcionales.",
        detalle: "Un ecosistema integral para tu empresa con Bitrix24.",
        boton: "Empieza Hoy",
        link: "/register",
    },
    {
        imagen: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80",
        titulo: "DESCUBRE NUESTRA DEMO",
        subtitulo: "Prueba nuestra tecnología sin compromiso.",
        detalle: "Explora cómo We Collab puede impulsar tu negocio.",
        boton: "Ir a Demo",
        link: "/demo",
    },
];

// Testimonios
const testimonios = [
    {
        nombre: "Laura Martínez",
        texto: "Configuré mi CRM desde mi móvil con facilidad.",
        imagen: "https://randomuser.me/api/portraits/women/1.jpg",
    },
    {
        nombre: "Diego Ramírez",
        texto: "Automatización sencilla desde cualquier dispositivo.",
        imagen: "https://randomuser.me/api/portraits/men/2.jpg",
    },
    {
        nombre: "Sofía Vargas",
        texto: "Aprendo Bitrix24 desde mi tablet sin complicaciones.",
        imagen: "https://randomuser.me/api/portraits/women/3.jpg",
    },
];

// Estadísticas
const stats = [
    { label: "Capacitaciones", value: 100, icon: "fas fa-video" },
    { label: "Estudiantes", value: 5000, icon: "fas fa-users" },
    { label: "Horas de Contenido", value: 200, icon: "fas fa-clock" },
];

// Categorías populares
const categoriasPopulares = [
    { nombre: "CRM", icon: "fas fa-handshake", color: "bg-cyan-600" },
    { nombre: "Automatización", icon: "fas fa-cogs", color: "bg-lime-600" },
    { nombre: "Diseño", icon: "fas fa-paint-brush", color: "bg-pink-600" },
];

// Características del sistema
const sistemaGestion = [
    {
        titulo: "Biblioteca Organizada",
        descripcion: "Tutoriales clasificados para acceso rápido.",
        icon: "fas fa-folder-open",
        color: "bg-cyan-600",
    },
    {
        titulo: "Actualización Constante",
        descripcion: "Contenido siempre al día con Bitrix24.",
        icon: "fas fa-sync-alt",
        color: "bg-lime-600",
    },
    {
        titulo: "Progreso y Seguimiento",
        descripcion: "Monitorea tu avance fácilmente.",
        icon: "fas fa-chart-line",
        color: "bg-pink-600",
    },
    {
        titulo: "Acceso Multiplataforma",
        descripcion: "Aprende desde cualquier dispositivo.",
        icon: "fas fa-laptop-mobile",
        color: "bg-blue-600",
    },
    {
        titulo: "Soporte Integrado",
        descripcion: "Resuelve dudas con nuestra comunidad.",
        icon: "fas fa-headset",
        color: "bg-purple-600",
    },
];

// Preguntas frecuentes
const faqs = ref([
    {
        pregunta: "¿Qué cubren los tutoriales?",
        respuesta: "Configuración, automatización y diseño en Bitrix24.",
        abierta: false,
    },
    {
        pregunta: "¿Cómo funciona el sistema?",
        respuesta:
            "Organiza tutoriales y sigue tu progreso en cualquier navegador.",
        abierta: false,
    },
    {
        pregunta: "¿Necesito una app?",
        respuesta: "No, nuestro sistema web es 100% multiplaforma.",
        abierta: false,
    },
]);

// Función para manejar el clic en los botones del carrusel
const handleCarouselButtonClick = (link) => {
    if (isLoggedIn.value) {
        // Si está logueado, redirige al enlace directamente
        router.visit(link);
    } else {
        // Si no está logueado, verifica si es la ruta de demo
        if (link === "/demo") {
            router.visit(link); // Permite acceso a /demo sin sesión
        } else {
            // Redirige a la página de error para cualquier otra ruta
            router.visit("/error");
        }
    }
};

// Funciones
const toggleVideoPlay = () => {
    if (videoRef.value) {
        isVideoPlaying.value ? videoRef.value.pause() : videoRef.value.play();
        isVideoPlaying.value = !isVideoPlaying.value;
    }
};

const toggleVideoSound = () => {
    if (videoRef.value) {
        videoRef.value.muted = !videoRef.value.muted;
        isVideoMuted.value = videoRef.value.muted;
    }
};

const handleCardClick = (tutorial) => {
    if (isLoggedIn.value) {
        router.visit(tutorial.link);
    } else {
        Swal.fire({
            title: "Iniciar sesión requerido",
            text: "Debes iniciar sesión para ver este contenido.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Iniciar sesión",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit("/login");
            }
        });
    }
};

const shareTutorial = (tutorial) => {
    const shareUrl = encodeURIComponent(tutorial.link);
    const shareText = encodeURIComponent(
        `Mira este tutorial de Bitrix24: ${tutorial.titulo}`
    );
    const twitterUrl = `https://twitter.com/intent/tweet?text=${shareText}&url=${shareUrl}`;
    window.open(twitterUrl, "_blank");
};

const toggleSwiperAutoplay = (swiper) => {
    isSwiperPaused.value = !isSwiperPaused.value;
    isSwiperPaused.value ? swiper.autoplay.stop() : swiper.autoplay.start();
};

const toggleFavorite = (tutorial) => {
    const index = favorites.value.findIndex((fav) => fav.id === tutorial.id);
    if (index === -1) {
        favorites.value.push(tutorial);
    } else {
        favorites.value.splice(index, 1);
    }
    localStorage.setItem("favorites", JSON.stringify(favorites.value));
};

const toggleFaq = (index) => {
    faqs.value[index].abierta = !faqs.value[index].abierta;
};

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
        { threshold: 0.1 }
    );

    document.querySelectorAll(".observe").forEach((el) => observer.observe(el));
});
</script>

<template>
    <Head title="We Collab - Capacitaciones Bitrix24">
        <meta
            name="description"
            content="Capacitaciones destacadas en Bitrix24 con We Collab: comunicación, tareas, ventas y más."
        />
        <meta
            name="keywords"
            content="Bitrix24, tutoriales, CRM, automatización, We Collab, tecnología"
        />
    </Head>
    <div class="bg-gray-50 min-h-screen font-sans antialiased">
        <Header
            :isLoginPage="true"
            :canLogin="true"
            :canRegister="true"
            :showHeaderBottom="true"
        />

        <!-- Carrusel -->
        <main
            class="relative w-full h-[50vh] sm:h-[60vh] md:h-[80vh] overflow-hidden"
        >
            <Swiper
                :modules="[Navigation, Pagination, Autoplay, EffectFade]"
                :spaceBetween="0"
                :slidesPerView="1"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{ delay: 5000, disableOnInteraction: false }"
                :effect="'fade'"
                :fadeEffect="{ crossFade: true }"
                class="w-full h-full"
                @swiper="(swiper) => (swiperInstance = swiper)"
            >
                <SwiperSlide>
                    <div class="relative w-full h-full">
                        <div class="video-container">
                            <video
                                ref="videoRef"
                                autoplay
                                muted
                                loop
                                playsinline
                                class="video"
                            >
                                <source
                                    src="/video/Bienvenidos.mp4"
                                    type="video/mp4"
                                />
                                Tu navegador no soporta video.
                            </video>
                            <div
                                class="absolute bottom-4 right-4 flex gap-3 z-20"
                            >
                                <button
                                    @click="toggleVideoPlay"
                                    class="bg-cyan-500/90 p-2 rounded-full text-white hover:bg-cyan-600 transition-transform"
                                    title="Pausar/Reproducir video"
                                >
                                    <i
                                        :class="
                                            isVideoPlaying
                                                ? 'fas fa-pause'
                                                : 'fas fa-play'
                                        "
                                        class="text-sm"
                                    ></i>
                                </button>
                                <button
                                    @click="toggleVideoSound"
                                    class="bg-cyan-500/90 p-2 rounded-full text-white hover:bg-cyan-600 transition-transform"
                                    title="Silenciar/Activar sonido"
                                >
                                    <i
                                        :class="
                                            isVideoMuted
                                                ? 'fas fa-volume-mute'
                                                : 'fas fa-volume-up'
                                        "
                                        class="text-sm"
                                    ></i>
                                </button>
                            </div>
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-gray-900/60 via-cyan-900/50 to-cyan-600/40"
                        ></div>
                        <div
                            class="relative z-10 text-center text-white max-w-5xl mx-auto flex flex-col justify-center h-full px-4 sm:px-6"
                        >
                            <img
                                src="/img/wecollab.png"
                                alt="Logo We Collab"
                                class="w-24 sm:w-32 md:w-40 mx-auto mb-6 animate__animated animate__fadeInDown"
                            />
                            <h1
                                class="text-3xl sm:text-4xl md:text-6xl font-extrabold tracking-tight text-shadow animate__animated animate__fadeInUp"
                            >
                                WE COLLAB & BITRIX24
                            </h1>
                            <p
                                class="mt-3 text-base sm:text-lg md:text-xl text-cyan-100 animate__animated animate__fadeInUp animate__delay-1s"
                            >
                                Tu espacio de trabajo ideal con tecnología
                                avanzada.
                            </p>
                            <p
                                class="mt-2 text-sm sm:text-base md:text-lg text-gray-200 animate__animated animate__fadeInUp animate__delay-2s"
                            >
                                Implementa Bitrix24 de manera sencilla y rápida
                                para maximizar tu ROI.
                            </p>
                            <button
                                @click="handleCarouselButtonClick('/contact')"
                                class="mt-6 bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-3 sm:px-8 sm:py-4 rounded-full font-semibold hover:from-cyan-600 hover:to-blue-700 transition-all text-base sm:text-lg shadow-lg animate__animated animate__fadeInUp animate__delay-3s"
                            >
                                Descubre Cómo
                            </button>
                        </div>
                    </div>
                </SwiperSlide>
                <SwiperSlide
                    v-for="(slide, index) in carouselSlides"
                    :key="index"
                >
                    <div
                        class="relative w-full h-full bg-cover bg-center flex items-center justify-center"
                        :style="{ backgroundImage: `url(${slide.imagen})` }"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-gray-900/60 via-cyan-900/50 to-cyan-600/40"
                        ></div>
                        <div
                            class="relative z-10 text-center text-white max-w-5xl mx-auto px-4 sm:px-6"
                        >
                            <img
                                src="/img/wecollab.png"
                                alt="Logo We Collab"
                                class="w-24 sm:w-32 md:w-40 mx-auto mb-6 animate__animated animate__fadeInDown"
                            />
                            <h1
                                class="text-3xl sm:text-4xl md:text-6xl font-extrabold tracking-tight text-shadow animate__animated animate__fadeInUp"
                            >
                                {{ slide.titulo }}
                            </h1>
                            <p
                                class="mt-3 text-base sm:text-lg md:text-xl text-cyan-100 animate__animated animate__fadeInUp animate__delay-1s"
                            >
                                {{ slide.subtitulo }}
                            </p>
                            <p
                                class="mt-2 text-sm sm:text-base md:text-lg text-gray-200 animate__animated animate__fadeInUp animate__delay-2s"
                            >
                                {{ slide.detalle }}
                            </p>
                            <button
                                @click="handleCarouselButtonClick(slide.link)"
                                class="mt-6 bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-3 sm:px-8 sm:py-4 rounded-full font-semibold hover:from-cyan-600 hover:to-blue-700 transition-all text-base sm:text-lg shadow-lg animate__animated animate__fadeInUp animate__delay-3s"
                            >
                                {{ slide.boton }}
                            </button>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
            <button
                @click="toggleSwiperAutoplay(swiperInstance)"
                class="absolute bottom-4 left-4 bg-cyan-500/90 p-2 rounded-full text-white hover:bg-cyan-600 transition-transform"
                title="Pausar/Reanudar carrusel"
            >
                <i
                    :class="isSwiperPaused ? 'fas fa-play' : 'fas fa-pause'"
                    class="text-sm"
                ></i>
            </button>
        </main>

        <!-- Por qué We Collab -->
        <section
            class="py-12 px-4 sm:px-6 bg-gradient-to-b from-white to-gray-50 observe"
        >
            <div class="max-w-6xl mx-auto text-center">
                <h2
                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 animate__animated animate__fadeInUp"
                >
                    ¿Por qué elegir We Collab?
                </h2>
                <p
                    class="text-base sm:text-lg md:text-xl text-gray-700 max-w-3xl mx-auto leading-relaxed animate__animated animate__fadeInUp animate__delay-1s"
                >
                    Expertos en Bitrix24 con un sistema web multiplaforma para
                    aprender desde cualquier dispositivo.
                </p>
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div
                        class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:-translate-y-2"
                    >
                        <i
                            class="fas fa-chalkboard-teacher text-4xl sm:text-5xl text-cyan-600 mb-4"
                        ></i>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                            Expertos Certificados
                        </h3>
                        <p
                            class="text-sm sm:text-base text-gray-600 leading-relaxed mt-2"
                        >
                            Instructores con experiencia real en Bitrix24.
                        </p>
                    </div>
                    <div
                        class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:-translate-y-2"
                    >
                        <i
                            class="fas fa-laptop-code text-4xl sm:text-5xl text-cyan-600 mb-4"
                        ></i>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                            Contenido Práctico
                        </h3>
                        <p
                            class="text-sm sm:text-base text-gray-600 leading-relaxed mt-2"
                        >
                            Videos diseñados para resultados inmediatos.
                        </p>
                    </div>
                    <div
                        class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:-translate-y-2"
                    >
                        <i
                            class="fas fa-globe text-4xl sm:text-5xl text-cyan-600 mb-4"
                        ></i>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                            Acceso Multiplaforma
                        </h3>
                        <p
                            class="text-sm sm:text-base text-gray-600 leading-relaxed mt-2"
                        >
                            Sistema web optimizado para cualquier dispositivo.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categorías Populares -->
        <section class="py-12 px-4 sm:px-6 bg-gray-100 observe">
            <h2
                class="text-center text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 animate__animated animate__fadeInUp"
            >
                Capacitaciones Populares
            </h2>
            <div
                class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-8"
            >
                <div
                    v-for="categoria in categoriasPopulares"
                    :key="categoria.nombre"
                    class="group relative rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all transform hover:-translate-y-2"
                    :class="categoria.color"
                >
                    <i
                        :class="
                            categoria.icon +
                            ' text-4xl sm:text-5xl text-white mb-4'
                        "
                    ></i>
                    <h3 class="text-lg sm:text-xl font-bold text-white">
                        {{ categoria.nombre }}
                    </h3>
                    <button
                        class="mt-4 bg-white text-gray-900 px-4 py-2 rounded-full font-semibold hover:bg-gray-200 text-sm sm:text-base shadow-md"
                        @click="selectedCategory = categoria.nombre"
                    >
                        Explorar
                    </button>
                </div>
            </div>
        </section>

        <!-- Capacitaciones Destacadas -->
        <section class="py-12 px-4 sm:px-6 bg-white observe">
            <h2
                class="text-center text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 animate__animated animate__fadeInUp"
            >
                Capacitaciones Destacadas
            </h2>
            <div
                class="max-w-6xl mx-auto mb-10 flex flex-col sm:flex-row justify-center gap-4 items-center"
            >
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Buscar capacitaciones..."
                    class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-cyan-500 w-full sm:w-80 text-sm shadow-md"
                />
                <div class="flex flex-wrap gap-3 mt-4 sm:mt-0 justify-center">
                    <button
                        v-for="category in ['Todos', 'CRM', 'Automatización']"
                        :key="category"
                        @click="selectedCategory = category"
                        :class="[
                            'px-4 py-2 rounded-full font-semibold text-sm sm:text-base',
                            selectedCategory === category
                                ? 'bg-cyan-600 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300',
                        ]"
                    >
                        {{ category }}
                    </button>
                </div>
            </div>
            <Swiper
                :modules="[Navigation, Pagination, Autoplay]"
                :spaceBetween="25"
                :slidesPerView="1"
                :breakpoints="{
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                }"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{ delay: 4000, disableOnInteraction: false }"
                class="w-full"
            >
                <SwiperSlide
                    v-for="(tutorial, index) in filteredTutoriales"
                    :key="index"
                >
                    <div
                        class="relative bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:-translate-y-2"
                        :class="{
                            'border-2 border-cyan-400': tutorial.esPopular,
                        }"
                    >
                        <img
                            :src="tutorial.imagen"
                            :alt="`Imagen de ${tutorial.titulo}`"
                            class="w-full h-36 sm:h-48 object-cover rounded-t-xl"
                            loading="lazy"
                        />
                        <div
                            v-if="tutorial.esNuevo"
                            class="absolute top-3 left-3 bg-cyan-600 text-white text-xs px-2 py-1 rounded-full"
                        >
                            Nuevo
                        </div>
                        <div
                            v-if="tutorial.esPopular"
                            class="absolute top-3 right-3 text-cyan-400 text-xl"
                        >
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="mt-4">
                            <h3
                                class="text-base sm:text-lg font-bold text-gray-800"
                            >
                                {{ tutorial.titulo }}
                            </h3>
                            <p
                                class="mt-2 text-sm sm:text-base text-gray-600 line-clamp-2 leading-relaxed"
                            >
                                {{ tutorial.descripcion }}
                            </p>
                            <div class="mt-3 text-sm text-gray-500">
                                <p>
                                    <strong>Categoría:</strong>
                                    {{ tutorial.categoria }}
                                </p>
                                <p>
                                    <strong>Duración:</strong>
                                    {{ tutorial.duracion }}
                                </p>
                            </div>
                            <div class="mt-3 h-2 bg-gray-200 rounded-full">
                                <div
                                    class="h-full bg-cyan-600 rounded-full"
                                    :style="{ width: `${tutorial.progreso}%` }"
                                ></div>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <button
                                    @click.stop="shareTutorial(tutorial)"
                                    class="bg-cyan-600 text-white px-4 py-2 rounded-full hover:bg-cyan-700 text-sm sm:text-base shadow-md"
                                >
                                    Compartir
                                </button>
                                <button
                                    @click.stop="toggleFavorite(tutorial)"
                                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-full hover:bg-gray-300 text-sm sm:text-base shadow-md"
                                >
                                    <i
                                        :class="
                                            favorites.some(
                                                (fav) => fav.id === tutorial.id
                                            )
                                                ? 'fas fa-heart text-red-500'
                                                : 'far fa-heart'
                                        "
                                    ></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
        </section>

        <!-- Acerca de Nosotros -->
        <section class="py-12 px-4 sm:px-6 bg-gray-100 observe">
            <div
                class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-10"
            >
                <div
                    class="w-full md:w-1/2 h-64 md:h-96 overflow-hidden rounded-2xl shadow-lg bg-white"
                >
                    <img
                        src="/img/wecollab.png"
                        alt="Equipo de We Collab"
                        class="w-full h-full object-contain p-6"
                        loading="lazy"
                    />
                </div>
                <div class="w-full md:w-1/2">
                    <h2
                        class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 animate__animated animate__fadeInUp"
                    >
                        Sobre We Collab
                    </h2>
                    <p
                        class="text-base sm:text-lg text-gray-700 leading-relaxed animate__animated animate__fadeInUp animate__delay-1s"
                    >
                        We Collab empodera negocios con capacitaciones
                        especializadas en Bitrix24. Nuestra misión es ofrecer
                        soluciones tecnológicas para comunicación, tareas y
                        ventas.
                    </p>
                    <p
                        class="mt-3 text-base sm:text-lg text-gray-700 leading-relaxed animate__animated animate__fadeInUp animate__delay-2s"
                    >
                        <strong>Visión:</strong> Ser líderes globales en
                        formación tecnológica.<br /><strong>Valores:</strong>
                        Innovación, excelencia y compromiso.
                    </p>
                    <button
                        class="mt-6 bg-cyan-600 text-white px-6 py-3 sm:px-8 sm:py-4 rounded-full font-semibold hover:bg-cyan-700 text-base sm:text-lg shadow-lg animate__animated animate__fadeInUp animate__delay-3s"
                        @click="router.visit('/about')"
                    >
                        Conoce Más
                    </button>
                </div>
            </div>
        </section>

        <!-- Sistema de Gestión de Tutoriales -->
        <section class="py-12 px-4 sm:px-6 bg-white observe">
            <div class="max-w-6xl mx-auto text-center">
                <h2
                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 animate__animated animate__fadeInUp"
                >
                    Nuestro Sistema de Gestión
                </h2>
                <p
                    class="text-base sm:text-lg text-gray-700 max-w-3xl mx-auto mb-10 leading-relaxed animate__animated animate__fadeInUp animate__delay-1s"
                >
                    Un sistema web multiplaforma que organiza y entrega
                    capacitaciones en Bitrix24 de forma eficiente.
                </p>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8"
                >
                    <div
                        v-for="feature in sistemaGestion"
                        :key="feature.titulo"
                        class="rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:-translate-y-2"
                        :class="feature.color"
                    >
                        <i
                            :class="
                                feature.icon +
                                ' text-4xl sm:text-5xl text-white mb-4'
                            "
                        ></i>
                        <h3 class="text-lg sm:text-xl font-bold text-white">
                            {{ feature.titulo }}
                        </h3>
                        <p
                            class="text-sm sm:text-base text-white/90 leading-relaxed mt-2"
                        >
                            {{ feature.descripcion }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonios -->
        <section class="py-12 px-4 sm:px-6 bg-gray-100 observe">
            <h2
                class="text-center text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 animate__animated animate__fadeInUp"
            >
                Lo que Dicen Nuestros Estudiantes
            </h2>
            <Swiper
                :modules="[Navigation, Pagination, Autoplay]"
                :spaceBetween="25"
                :slidesPerView="1"
                :breakpoints="{
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                }"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{ delay: 5000, disableOnInteraction: false }"
                class="w-full"
            >
                <SwiperSlide
                    v-for="(testimonio, index) in testimonios"
                    :key="index"
                >
                    <div
                        class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all transform hover:-translate-y-2"
                    >
                        <img
                            :src="testimonio.imagen"
                            :alt="`Foto de ${testimonio.nombre}`"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto mb-4 object-cover border-2 border-cyan-300"
                            loading="lazy"
                        />
                        <p
                            class="text-sm sm:text-base text-gray-600 italic leading-relaxed"
                        >
                            “{{ testimonio.texto }}”
                        </p>
                        <h3
                            class="mt-3 text-base sm:text-lg font-bold text-gray-800"
                        >
                            {{ testimonio.nombre }}
                        </h3>
                    </div>
                </SwiperSlide>
            </Swiper>
        </section>

        <!-- Estadísticas -->
        <section
            class="py-12 px-4 sm:px-6 bg-gradient-to-r from-cyan-600 to-blue-600 text-white observe"
        >
            <div
                class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-10 text-center"
            >
                <div v-for="stat in stats" :key="stat.label">
                    <i :class="stat.icon + ' text-4xl sm:text-5xl mb-4'"></i>
                    <h3 class="text-3xl sm:text-4xl font-extrabold">
                        {{ stat.value }}+
                    </h3>
                    <p class="text-base sm:text-lg">{{ stat.label }}</p>
                </div>
            </div>
        </section>

        <!-- Preguntas Frecuentes -->
        <section class="py-12 px-4 sm:px-6 bg-white observe">
            <h2
                class="text-center text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 animate__animated animate__fadeInUp"
            >
                Preguntas Frecuentes
            </h2>
            <div class="max-w-4xl mx-auto">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="bg-gray-50 rounded-2xl shadow-lg mb-4 overflow-hidden"
                >
                    <button
                        class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-cyan-50 transition-colors"
                        @click="toggleFaq(index)"
                        :aria-expanded="faq.abierta"
                        :aria-controls="'faq-' + index"
                    >
                        <span
                            class="text-base sm:text-lg font-bold text-gray-800"
                            ><i
                                class="fas fa-question-circle mr-3 text-cyan-600"
                            ></i
                            >{{ faq.pregunta }}</span
                        >
                        <i
                            :class="
                                faq.abierta
                                    ? 'fas fa-chevron-up'
                                    : 'fas fa-chevron-down'
                            "
                            class="text-cyan-600 text-lg"
                        ></i>
                    </button>
                    <div
                        v-show="faq.abierta"
                        :id="'faq-' + index"
                        class="px-6 py-4 text-sm sm:text-base text-gray-600 bg-white leading-relaxed"
                    >
                        {{ faq.respuesta }}
                    </div>
                </div>
            </div>
        </section>

        <!-- Dispositivo Móvil -->
        <section class="py-12 px-4 sm:px-6 bg-gray-100 observe">
            <div
                class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-10"
            >
                <div class="w-full md:w-1/2">
                    <h2
                        class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 animate__animated animate__fadeInUp"
                    >
                        Bitrix24 en Cualquier Dispositivo
                    </h2>
                    <p
                        class="text-base sm:text-lg text-gray-700 leading-relaxed animate__animated animate__fadeInUp animate__delay-1s"
                    >
                        Accede a nuestras capacitaciones desde cualquier
                        navegador, sin necesidad de apps.
                    </p>
                    <button
                        class="mt-6 bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-3 sm:px-8 sm:py-4 rounded-full font-semibold hover:from-cyan-600 hover:to-blue-700 text-base sm:text-lg shadow-lg animate__animated animate__fadeInUp animate__delay-2s"
                        @click="router.visit('/register')"
                    >
                        Comienza Ahora
                    </button>
                </div>
                <div class="w-full md:w-1/2 flex justify-center md:justify-end">
                    <div class="w-56 sm:w-72 md:w-80"><Prueba /></div>
                </div>
            </div>
        </section>

        <!-- Llamada a la Acción -->
        <section
            class="py-12 px-4 sm:px-6 bg-gradient-to-r from-cyan-700 to-blue-700 text-white text-center observe"
        >
            <div class="max-w-4xl mx-auto">
                <h2
                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-6 animate__animated animate__fadeInUp"
                >
                    ¡Domina Bitrix24 Hoy!
                </h2>
                <p
                    class="text-base sm:text-lg mb-8 leading-relaxed animate__animated animate__fadeInUp animate__delay-1s"
                >
                    Transforma tu negocio con We Collab desde cualquier
                    dispositivo.
                </p>
                <button
                    class="bg-gradient-to-r from-lime-500 to-cyan-500 text-white px-8 py-4 sm:px-10 sm:py-5 rounded-full font-semibold hover:from-lime-600 hover:to-cyan-600 text-lg sm:text-xl shadow-lg animate__animated animate__fadeInUp animate__delay-2s"
                    @click="router.visit('/register')"
                >
                    Regístrate Ahora
                </button>
            </div>
        </section>

        <Footer class="mt-12" />
    </div>
</template>

<style scoped>
/* Estilos generales */
.bg-gray-50 {
    font-family: "Inter", sans-serif;
    background: #f9fafb;
    color: #1f2937;
}

/* Video */
.video-container {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Sombra para textos */
.text-shadow {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
}

/* Animaciones */
.animate__animated {
    animation-duration: 1s;
}

.animate__fadeInDown {
    animation-name: fadeInDown;
}
.animate__fadeInUp {
    animation-name: fadeInUp;
}
.animate__delay-1s {
    animation-delay: 0.5s;
}
.animate__delay-2s {
    animation-delay: 1s;
}
.animate__delay-3s {
    animation-delay: 1.5s;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Línea de texto truncada */
.line-clamp-2 {
    display: -webkit-box;

    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsividad */
@media (max-width: 640px) {
    .swiper-slide {
        padding: 0 10px;
    }
    .h-48 {
        height: 10rem;
    }
    .text-6xl {
        font-size: 2.25rem;
    }
    .text-4xl {
        font-size: 1.75rem;
    }
    .text-3xl {
        font-size: 1.5rem;
    }
    .text-xl {
        font-size: 1.125rem;
    }
    .text-lg {
        font-size: 1rem;
    }
    .text-base {
        font-size: 0.875rem;
    }
    .text-sm {
        font-size: 0.75rem;
    }
    .px-6 {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
    }
    .py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .p-6 {
        padding: 1.25rem;
    }
    .mt-8 {
        margin-top: 2rem;
    }
    .mb-8 {
        margin-bottom: 2rem;
    }
}

@media (max-width: 1024px) {
    .h-96 {
        height: 20rem;
    }
}
</style>
