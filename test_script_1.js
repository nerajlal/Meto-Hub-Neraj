
    // Resend code countdown state
    let resendTimer = null;
    let countdownSeconds = 0;
    let currentStep = 1;

    document.addEventListener('DOMContentLoaded', () => {
      const triggerButtons = document.querySelectorAll('.pricing-btn-trigger');
      const saasModal = document.getElementById('saasModal');
      const closeSaasBtn = document.getElementById('closeSaasBtn');
      const planSelector = document.getElementById('saas_plan');

      // Auto-trigger registration modal if URL query has get_started=1
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('get_started') && saasModal) {
        if (planSelector) {
          planSelector.value = 'sprout';
          updatePlanBadges('sprout');
        }
        saasModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        showSaasStage('saasStageSignUp');
        goToStep(1);
      }

      triggerButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const plan = btn.getAttribute('data-plan');
          if (planSelector && plan) {
            planSelector.value = plan;
            updatePlanBadges(plan);
          }

          // Show Modal
          saasModal.classList.add('active');
          document.body.style.overflow = 'hidden';

          // Reset stages and onboarding wizard step
          showSaasStage('saasStageSignUp');
          goToStep(1);
        });
      });

      if (planSelector) {
        planSelector.addEventListener('change', () => {
          updatePlanBadges(planSelector.value);
        });
      }

      const countrySelector = document.getElementById('saas_country');
      const phoneCodeSelector = document.getElementById('saas_phone_code');
      if (countrySelector && phoneCodeSelector) {
        const countryToCode = {
          'India': '+91',
          'United Arab Emirates': '+971',
          'Saudi Arabia': '+966',
          'United Kingdom': '+44',
          'United States': '+1',
          'France': '+33',
          'Singapore': '+65',
          'Australia': '+61'
        };
        countrySelector.addEventListener('change', () => {
          const countryVal = countrySelector.value;
          const matchingCode = countryToCode[countryVal];
          if (matchingCode) {
            phoneCodeSelector.value = matchingCode;
          }
        });
      }

      closeSaasBtn.addEventListener('click', closeSaaSModal);

      saasModal.addEventListener('click', (e) => {
        if (e.target === saasModal) {
          closeSaaSModal();
        }
      });
    });

    function updatePlanBadges(planName) {
      const capitalized = planName.charAt(0).toUpperCase() + planName.slice(1);
      document.querySelectorAll('.class-plan-badge').forEach(badge => {
        badge.textContent = capitalized;
      });
    }

    function validateStep(step) {
      let stepValid = true;

      // Clear error tags for the current step
      if (step === 1) {
        document.getElementById('err_name').textContent = '';
        document.getElementById('err_email').textContent = '';

        const name = document.getElementById('saas_name').value.trim();
        const email = document.getElementById('saas_email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name) {
          document.getElementById('err_name').textContent = 'Full name is required';
          stepValid = false;
        }
        if (!email) {
          document.getElementById('err_email').textContent = 'Email address is required';
          stepValid = false;
        } else if (!emailRegex.test(email)) {
          document.getElementById('err_email').textContent = 'Please enter a valid email address';
          stepValid = false;
        }
      } else if (step === 2) {
        document.getElementById('err_business').textContent = '';
        document.getElementById('err_country').textContent = '';
        document.getElementById('err_whatsapp').textContent = '';

        const business = document.getElementById('saas_business').value.trim();
        const country = document.getElementById('saas_country').value;
        const whatsapp = document.getElementById('saas_whatsapp').value.trim();
        const whatsappRegex = /^\+?[0-9\s\-()]{7,18}$/;

        if (!business) {
          document.getElementById('err_business').textContent = 'Business name is required';
          stepValid = false;
        }
        if (!country) {
          document.getElementById('err_country').textContent = 'Please select your country';
          stepValid = false;
        }
        if (!whatsapp) {
          document.getElementById('err_whatsapp').textContent = 'WhatsApp number is required';
          stepValid = false;
        } else if (!whatsappRegex.test(whatsapp)) {
          document.getElementById('err_whatsapp').textContent = 'Please enter a valid phone number';
          stepValid = false;
        }
      } else if (step === 3) {
        document.getElementById('err_password').textContent = '';
        document.getElementById('err_confirm_password').textContent = '';

        const password = document.getElementById('saas_password').value;
        const confirmPassword = document.getElementById('saas_confirm_password').value;

        if (!password) {
          document.getElementById('err_password').textContent = 'Password is required';
          stepValid = false;
        } else if (password.length < 8) {
          document.getElementById('err_password').textContent = 'Password must be at least 8 characters';
          stepValid = false;
        }
        if (password !== confirmPassword) {
          document.getElementById('err_confirm_password').textContent = 'Passwords do not match';
          stepValid = false;
        }
      }
      return stepValid;
    }

    function goToStep(step) {
      // If navigating forward, validate preceding steps
      if (step > currentStep) {
        for (let i = currentStep; i < step; i++) {
          if (!validateStep(i)) return;
        }
      }

      currentStep = step;

      // Toggle form steps visibility
      document.querySelectorAll('.saas-form-step').forEach((el, idx) => {
        if (idx + 1 === step) {
          el.classList.add('active');
        } else {
          el.classList.remove('active');
        }
      });

      // Toggle dots active state
      document.querySelectorAll('#saasStepDots .saas-dot').forEach((el, idx) => {
        if (idx + 1 === step) {
          el.classList.add('active');
        } else {
          el.classList.remove('active');
        }
      });

      // Toggle Back button visibility
      const backBtn = document.getElementById('saasBackBtn');
      if (step === 1) {
        backBtn.style.visibility = 'hidden';
      } else {
        backBtn.style.visibility = 'visible';
      }

      // Update Next button label
      const nextBtn = document.getElementById('saasNextBtn');
      if (step === 3) {
        nextBtn.textContent = 'Create my account →';
      } else {
        nextBtn.textContent = 'Next';
      }
    }

    function prevStep() {
      if (currentStep > 1) {
        goToStep(currentStep - 1);
      }
    }

    function nextStep() {
      if (currentStep < 3) {
        if (validateStep(currentStep)) {
          goToStep(currentStep + 1);
        }
      }
    }

    function handleStepNavNext() {
      if (currentStep === 3) {
        handleSaasRegister(new Event('submit'));
      } else {
        nextStep();
      }
    }

    function closeSaaSModal() {
      const saasModal = document.getElementById('saasModal');
      saasModal.classList.remove('active');
      document.body.style.overflow = '';
      // Clear resend countdown timer if active
      if (resendTimer) {
        clearInterval(resendTimer);
        resendTimer = null;
      }
    }

    function showSaasStage(stageId) {
      document.querySelectorAll('.saas-stage').forEach(stage => {
        stage.classList.remove('active');
      });
      const target = document.getElementById(stageId);
      if (target) target.classList.add('active');
    }

    function handleSaasRegister(e) {
      if (e) e.preventDefault();

      // Verify all steps are fully valid
      if (!validateStep(1) || !validateStep(2) || !validateStep(3)) {
        return;
      }

      const nextBtn = document.getElementById('saasNextBtn');
      const originalText = nextBtn.textContent;
      nextBtn.disabled = true;
      nextBtn.textContent = 'Registering...';

      const name = document.getElementById('saas_name').value.trim();
      const business = document.getElementById('saas_business').value.trim();
      const country = document.getElementById('saas_country').value;
      const email = document.getElementById('saas_email').value.trim();
      const whatsappPrefix = document.getElementById('saas_phone_code').value;
      const whatsappNumber = document.getElementById('saas_whatsapp').value.trim();
      const password = document.getElementById('saas_password').value;
      const plan = document.getElementById('saas_plan').value;
      const theme = document.getElementById('saas_theme').value;

      const fullWhatsapp = whatsappPrefix + ' ' + whatsappNumber;

      fetch('""', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '""',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: name,
          business: business,
          country: country,
          email: email,
          whatsapp: fullWhatsapp,
          password: password,
          plan: plan,
          theme: theme
        })
      })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
          nextBtn.disabled = false;
          nextBtn.textContent = originalText;

          if (status === 200 && body.success) {
            // Success! Show Step 2: Email verification
            document.getElementById('verifyEmailDisplay').textContent = email;

            // Store the dynamic verification URL on the simulator button!
            const simBtn = document.querySelector('.saas-simulate-btn');
            if (simBtn && body.verify_url) {
              simBtn.setAttribute('onclick', `window.location.href="${body.verify_url}"`);
            }

            showSaasStage('saasStageVerify');
            startResendCountdown();
          } else {
            // Handle validation errors from backend
            if (body.errors) {
              Object.keys(body.errors).forEach(key => {
                const errEl = document.getElementById(`err_${key}`);
                if (errEl) {
                  errEl.textContent = body.errors[key][0];
                }
              });

              // Switch to the step with errors
              if (body.errors.name || body.errors.email) {
                goToStep(1);
              } else if (body.errors.business || body.errors.country || body.errors.whatsapp) {
                goToStep(2);
              } else if (body.errors.password || body.errors.theme) {
                goToStep(3);
              }
            } else {
              alert(body.message || 'An unexpected error occurred. Please try again.');
            }
          }
        })
        .catch(error => {
          nextBtn.disabled = false;
          nextBtn.textContent = originalText;
          console.error('Error:', error);
          alert('A connection error occurred. Please check your network and try again.');
        });
    }

    function startResendCountdown() {
      const resendBtn = document.getElementById('resendBtn');
      const resendCountdown = document.getElementById('resendCountdown');

      if (resendTimer) clearInterval(resendTimer);

      countdownSeconds = 60;
      resendBtn.disabled = true;
      resendCountdown.textContent = `You can resend the link in ${countdownSeconds}s`;

      resendTimer = setInterval(() => {
        countdownSeconds--;
        if (countdownSeconds <= 0) {
          clearInterval(resendTimer);
          resendTimer = null;
          resendBtn.disabled = false;
          resendCountdown.textContent = '';
        } else {
          resendCountdown.textContent = `You can resend the link in ${countdownSeconds}s`;
        }
      }, 1000);
    }

    function handleResendCode() {
      alert('A new verification email has been sent successfully!');
      startResendCountdown();
    }

    function simulateVerificationSuccess() {
      showSaasStage('saasStageSuccess');
    }
  