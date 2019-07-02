<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          <?php
            if(have_posts()){
              while(have_posts()){
                the_post();

                the_content();
              }
            }
          ?>

          <ol type="I">
            <li class="bop-question section_1">
              <p>Is your client a first timer young male offender 32 years of age or younger, facing a sentence of 60 months or more?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_1-1" aria-expanded="false" aria-controls="section_1-1">Yes</button>
              </p>
              <div id="section_1-1" class="collapse">
                <p>Would your client be interested in participating in a program that teaches how to create a smoother adjustment to federal Prison?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_1-2" aria-expanded="false" aria-controls="section_1-2">Yes</button>
                </p>
              </div>
              <div id="section_1-2" class="collapse">
                <p>Will the be sentenced to a medium security facility?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_1-3" aria-expanded="false" aria-controls="section_1-3">Yes</button>
                </p>
              </div>
              <div id="section_1-3" class="collapse">
                <p><strong>This program may help</strong></p>
                <p><a href="https://www.pprsus.com/programs/bureau-rehabilitation-and-values-enhancement-program/">Brave Program</a> - <strong>Facility Locations:</strong> FCI Victorville, CA-Medium; PCI Beckley, WV-Medium</p>
              </div>
            </li>

            <li class="bop-question section_2">
              <p>Is your client a male inmate in (or facing) a high security penitentiary setting with a history of substance abuse / dependence or a major mental illness as evidenced by a current diagnosis of a <strong>Psychotic Disorder</strong> that may include: Mood, Anxiety, Schizophrenia, Delusion and/or a Substance-induced Psychotic Disorder?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_2-1" aria-expanded="false" aria-controls="section_2-1">Yes</button>
              </p>
              <div id="section_2-1" class="collapse">
                <p><strong>This program may help</strong></p>
                <p><a href="https://www.pprsus.com/programs/challenge-program/">Challenge Program</a> - <strong>USP Facility Locations:</strong></p>
                <ul class="list-unstyled">
                  <li>USP Big Sandy, KY-High</li>
                  <li>USP McCreary, KY-High</li>
                  <li>USP Beaumont, TX-High</li>
                  <li>USP Pollock, LA-High</li>
                  <li>USP Terre Haute, IN-High</li>
                  <li>USP Hazelton, WV-High</li>
                  <li>USP Allenwood, PA-High</li>
                  <li>USP Coleman I, FL-High</li>
                  <li>USP Tuscon, AZ-High</li>
                  <li>USP Coleman II, FL-High</li>
                  <li>USP Lee, VA-High</li>
                  <li>USP Canaan, PA-High</li>
                  <li>USP Atwater, CA-High</li>
                </ul>
              </div>
            </li>

            <li class="bop-question section_3">
              <p>Is your client a male or female with a serious mental illness, but who does not require inpatient treatment?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_3-1" aria-expanded="false" aria-controls="section_3-1">Yes</button>
              </p>
              <div id="section_3-1" class="collapse">
                <p>Do they lack the skills to function in a general population prison setting?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_3-2" aria-expanded="false" aria-controls="section_3-2">Yes</button>
                </p>
              </div>
              <div id="section_3-2" class="collapse">
                <p>Would they be interested in a program that works closely with Psychiatry Services to ensure they receive appropriate medication and have the opportunity to build a positive relationship with the treating psychiatrist?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_3-3" aria-expanded="false" aria-controls="section_3-3">Yes</button>
              </div>
              <div id="section_3-3" class="collapse">
                <p><strong>This program may help</strong></p>
                <p><a href="https://www.pprsus.com/programs/mental-health-step-down-program/">Mental Health Step Down Program</a> - <strong>USP Facility Locations:</strong></p>
                <ul class="list-unstyled">
                  <li>FCI Butner, NC-Medium</li>
                  <li>USP Atlanta, GA-High</li>
                </ul>
                <p>* Male inmates with a primary diagnosis of Borderline Personality disorder are referred to the STAGES Program.</p>
              </div>
            </li>

            <li class="bop-question section_4">
              <p>Is your client a male or female with a history of mental illness related to physical or mental trauma, or to traumatic PSTD?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_4-1" aria-expanded="false" aria-controls="section_4-1">Yes</button>
              </p>
              <div id="section_4-1" class="collapse">
                <p>Would your client be interested in a program that focuses on the development of personal resilience, effective coping skills, emotional self-regulation, and healthy interpersonal relationships?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_4-2" aria-expanded="false" aria-controls="section_4-2">Yes</button>
                </p>
              </div>
              <div id="section_4-2" class="collapse">
                <p><strong>This program may help</strong></p>
                <p><a href="https://www.pprsus.com/programs/resolve-program/">Resolve Program</a> - <strong>USP Facility Locations:</strong></p>
                <ul class="list-unstyled">
                  <li>FPC Alderson, WV-Minimum (F)</li>
                  <li>SCP Lexington, KY-Minimum (F)</li>
                  <li>SCP Greenville, IL-Minimum (F)</li>
                  <li>FCI Aliceville, AL-Low (F)</li>
                  <li>SCP Marianna, FL-Minimum (F)</li>
                  <li>FCI Dublin, CA-Low (F)</li>
                  <li>FCI Danbury, CT-Low (M)</li>
                  <li>FSL Danbury, CT-Low (F) (Activating)</li>
                  <li>FMC Carswell, TX-Adm. (F)</li>
                  <li>SFF Hazelton, WV-Low (F)</li>
                  <li>ADX Florence, CO Maximum (M)</li>
                  <li>FCI Waseca, MN-Low (F)</li>
                  <li>SCP Coleman, FL-Minimum (F)</li>
                  <li>FCI Tallahassee, FL-Low (F)</li>
                  <li>SCP Victorville, CA-Minimum (F)</li>
                  <li>SCP Danbury, CT-Minimum (F)</li>
                  <li>FFPC Bryan, TX-Minimum (F)</li>
                </ul>
              </div>
            </li>

            <li class="bop-question section_5">
              <p>Does your client have a significant functional impairment due to intellectual disabilities, neurological deficits, and/or remarkable social skills deficits? For example, do any of these apply to your client: Autism Spectrum Disorder, Obsessive-Compulsive disorder, Epilepsy, Alzheimer's, Parkinson's or Traumatic brain injuries (TBIs) to mention just a few?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_5-1" aria-expanded="false" aria-controls="section_5-1">Yes</button>
              </p>
              <div id="section_5-1" class="collapse">
                <p>Would your client be interested in a program designed to increase their academic achievement and adaptive behavior of being cognitively impaired, thereby improving their institutional adjustment and likelihood for successful community reentry?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_5-2" aria-expanded="false" aria-controls="section_5-2">Yes</button>
                </p>
              </div>
              <div id="section_5-2" class="collapse">
                <p><strong>This program may help</strong></p>
                <p><a href="https://www.pprsus.com/programs/skills-program/">Skills Program</a> - <strong>USP Facility Locations:</strong></p>
                <ul class="list-unstyled">
                  <li>FCI Coleman, FL-Medium</li>
                  <li>FCI Danbury, CT-Low</li>
                </ul>
              </div>
            </li>

            <li class="bop-question section_6">
              <p>Is your client a male inmate (or facing prison) with a serious mental illness and a primary diagnosis of Borderline Personality Disorder, along with a history of unfavorable institutional adjustment linked to this disorder?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_6-1" aria-expanded="false" aria-controls="section_6-1">Yes</button>
              </p>
              <div id="section_6-1" class="collapse">
                <p>Would they be willing to volunteer for the program?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_6-2" aria-expanded="false" aria-controls="section_6-2">Yes</button>
                </p>
              </div>
              <div id="section_6-2" class="collapse">
                <p><strong>This program may help</strong></p>
                <p><a href="https://www.pprsus.com/programs/stages/">Stages Program</a> - <strong>USP Facility Locations:</strong></p>
                <ul class="list-unstyled">
                  <li>FCI Terre Haute, IN-Medium</li>
                  <li>USP Florence, CO-High (Effective 9/2014)</li>
                </ul>
              </div>
            </li>

            <li class="bop-question section_7">
              <p><strong>Sex Offender Conviction</strong></p>
            </li>

            <li class="bop-question section_8">
              <p>Is your client considered a low to moderate risk sexual offender?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_8-1" aria-expanded="false" aria-controls="section_8-1">Yes</button>
              </p>
              <div id="section_8-1" class="collapse">
                <p>Does your client have a history of a single sex crime; or are they serving a sentence for first time Internet Sex Offense?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_8-2" aria-expanded="false" aria-controls="section_8-2">Yes</button>
                </p>
              </div>
              <div id="section_8-2" class="collapse">
                <p><strong>This program may help</strong></p>
                <p>SOTP-NR Program - <strong>USP Facility Locations:</strong></p>
                <ul class="list-unstyled">
                  <li>FCI Petersburg - Medium</li>
                  <li>FCI Elkton, OH-Low</li>
                  <li>FCI Seagoville, TX-Low</li>
                  <li>FCI Englewood, CO-Low</li>
                  <li>FMC Carswell, Tx-Med. Ctr. (Females)</li>
                  <li>FCI Marianna, FL-Medium</li>
                  <li>USP Marion, IL-Medium</li>
                  <li USP Tucson, AZ-High</li>
                </ul>
              </div>
            </li>

            <li class="bop-question section_9">
              <p>Is your client considered a high risk sexual offender?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_9-1" aria-expanded="false" aria-controls="section_9-1">Yes</button>
              </p>
              <div id="section_9-1" class="collapse">
                <p>Does your client have a history of multiple sex crimes (re-offense sex offender), extensive non-sexual criminal histories, and/or a high level of sexual deviancy or hypersexuality?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_9-2" aria-expanded="false" aria-controls="section_9-2">Yes</button>
                </p>
              </div>
              <div id="section_9-2" class="collapse">
                <p>Does their criminal history include rape, sodomy, incest, carnal knowledge, transportation with coercion, force for commercial purposes or sexual exploitation of children, unlawful sexual conduct with a minor and/or internet pornography?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_9-3" aria-expanded="false" aria-controls="section_9-3">Yes</button>
                </p>
              </div>
              <div id="section_9-3" class="collapse">
                <p><strong>This program may help</strong></p>
                <p>SOTP-R Program - <strong>Facility Locations:</strong></p>
                <ul class="list-unstyled">
                  <li>USP Marion, IL-Medium / High</li>
                  <li>FMC Devens, MA-Med. Ctr.</li>
                </ul>
              </div>
            </li>

            <li class="bop-question section_10">
              <p>New: Commitment and Treatment program for <a href="https://www.bop.gov/jobs/docs/bux_intern_brochure.pdf">Sexually Dangerous</a> Persons.</p>
              <p>Is your client a candidate for psychological treatment, implementation of a behavior management plan, and coordination of a multidisciplinary treatment team?
                <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_10-1" aria-expanded="false" aria-controls="section_10-1">Yes</button>
              </p>
              <div id="section_10-1" class="collapse">
                <p>Can your client be considered sexually dangerous with the possibility of criminal recidivism?
                  <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_10-2" aria-expanded="false" aria-controls="section_10-2">Yes</button>
                </p>
              </div>
              <div id="section_10-2" class="collapse">
                <p><strong>This program may help</strong></p>
                <p><a href="https://www.bop.gov/jobs/docs/bux_intern_brochure.pdf">Butner "New" Commitment and Treatment Program</a> - <strong>Facility Location:</strong></p>
                <ul class="list-unstyled">
                  <li>FCC Butner, NC
                </ul>
              </div>
            </li>
          </ol>

        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();