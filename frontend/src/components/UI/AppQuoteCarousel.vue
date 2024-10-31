<template>
  <div class="col-lg-6">
    <div class="p-lg-5 p-4 auth-one-bg h-100">
      <div class="bg-overlay"></div>
      <div class="position-relative h-100 d-flex flex-column">
        <div class="mb-4">
          <a href="index.html" class="d-block">
            <img src="/assets_velzon/images/logo-light.png" alt="Logo" height="18">
          </a>
        </div>
        <div class="mt-auto">
          <div id="quotesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
              <button type="button" data-bs-target="#quotesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
              <button type="button" data-bs-target="#quotesCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
              <button type="button" data-bs-target="#quotesCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner text-center text-white pb-5">
              <div class="carousel-item active">
                <p class="fs-15 fst-italic">" O sistema escolar transformou a forma como nossos alunos aprendem, tornando o aprendizado mais interativo e divertido! "</p>
              </div>
              <div class="carousel-item">
                <p class="fs-15 fst-italic">" Com uma gestão eficiente, conseguimos melhorar a comunicação entre pais, alunos e professores. "</p>
              </div>
              <div class="carousel-item">
                <p class="fs-15 fst-italic">" Adoramos a flexibilidade do sistema e como ele se adapta às necessidades da nossa escola. "</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppQuoteCarousel',
};
</script>
