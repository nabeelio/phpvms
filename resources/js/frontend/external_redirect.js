export default () => {
  document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('a');

    links.forEach((link) => {
      const href = link.getAttribute('href');

      // Check if the link is external
      if (((href.startsWith('http://') || href.startsWith('https://')) && !href.includes(window.location.host)) || link.getAttribute('data-external-redirect') !== null) {
        link.addEventListener('click', (e) => {
          // We prevent the link from opening
          e.preventDefault();

          let externalHost;
          if (link.getAttribute('data-external-redirect') !== null) {
            externalHost = new URL(link.getAttribute('data-external-redirect')).hostname;
          } else {
            externalHost = new URL(href).hostname;
          }

          $('#externalRedirectHost').html(externalHost);
          $('#externalRedirectUrl').attr('href', href);
          $('#externalRedirectModal').modal('show');
        });
      }
    });

    document.querySelector('#externalRedirectUrl').addEventListener('click', () => {
      $('#externalRedirectModal').modal('hide');
    });
  });
};
