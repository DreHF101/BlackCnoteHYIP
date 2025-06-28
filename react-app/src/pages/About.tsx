import React from 'react';
import { Target, Users, Shield, TrendingUp, Heart } from 'lucide-react';

const About = () => {
  const values = [
    {
      icon: Target,
      title: 'Our Mission',
      description: 'To flip the Black-White wealth gap by 2040 through strategic community investments and financial empowerment.',
    },
    {
      icon: Users,
      title: 'Community First',
      description: 'Every investment circulates wealth within the Black community, supporting Black-owned businesses and projects.',
    },
    {
      icon: Shield,
      title: 'Transparency',
      description: 'We operate with complete transparency, providing clear information about risks, returns, and operations.',
    },
    {
      icon: Heart,
      title: 'Empowerment',
      description: 'We believe in empowering our community through financial education and accessible investment opportunities.',
    },
  ];

  const stats = [
    { label: 'Community Members', value: '1,200+' },
    { label: 'Total Invested', value: '$2.5M+' },
    { label: 'Black Businesses Supported', value: '150+' },
    { label: 'Success Rate', value: '98.5%' },
  ];

  const team = [
    {
      name: 'Marcus Johnson',
      role: 'Founder & CEO',
      image: 'https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=400',
      bio: 'Former Wall Street analyst with 15+ years in investment management, dedicated to building Black wealth.',
    },
    {
      name: 'Keisha Williams',
      role: 'Chief Investment Officer',
      image: 'https://images.pexels.com/photos/3760263/pexels-photo-3760263.jpeg?auto=compress&cs=tinysrgb&w=400',
      bio: 'Harvard MBA with expertise in alternative investments and community development finance.',
    },
    {
      name: 'Jamal Thompson',
      role: 'Head of Technology',
      image: 'https://images.pexels.com/photos/3785079/pexels-photo-3785079.jpeg?auto=compress&cs=tinysrgb&w=400',
      bio: 'Former tech lead at major fintech companies, ensuring secure and scalable platform operations.',
    },
    {
      name: 'Aisha Davis',
      role: 'Community Relations Director',
      image: 'https://images.pexels.com/photos/3760067/pexels-photo-3760067.jpeg?auto=compress&cs=tinysrgb&w=400',
      bio: 'Community organizer and financial educator passionate about economic empowerment.',
    },
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center space-y-6">
            <h1 className="text-5xl font-bold">About BlackCnote</h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              We're on a mission to flip the Black-White wealth gap by 2040 through strategic 
              investments, community empowerment, and financial education.
            </p>
          </div>
        </div>
      </section>

      {/* Mission & Values */}
      <section className="py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center space-y-4 mb-16">
            <h2 className="text-4xl font-bold text-gray-900">Our Values</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Everything we do is guided by our commitment to the Black community and 
              our vision of economic empowerment.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {values.map((value, index) => (
              <div key={index} className="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                  <value.icon className="h-6 w-6 text-white" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">{value.title}</h3>
                <p className="text-gray-600 leading-relaxed">{value.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="bg-gradient-to-r from-yellow-500 to-yellow-600 py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center text-white">
                <div className="text-4xl font-bold mb-2">{stat.value}</div>
                <div className="text-lg opacity-90">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Story Section */}
      <section className="py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div className="space-y-6">
              <h2 className="text-4xl font-bold text-gray-900">Our Story</h2>
              <div className="space-y-4 text-gray-600 leading-relaxed">
                <p>
                  BlackCnote was founded in 2019 with a simple but powerful vision: to create 
                  a platform where Black people could invest in their own community and build 
                  generational wealth together.
                </p>
                <p>
                  Recognizing that traditional investment platforms often exclude or underserve 
                  Black communities, we set out to create something different. A platform that 
                  not only provides competitive returns but ensures that every dollar invested 
                  stays within and strengthens the Black community.
                </p>
                <p>
                  Today, we're proud to have helped over 1,200 community members grow their 
                  wealth while supporting 150+ Black-owned businesses and community projects. 
                  But we're just getting started on our journey to flip the wealth gap by 2040.
                </p>
              </div>
            </div>
            <div className="relative">
              <img 
                src="https://images.pexels.com/photos/3760067/pexels-photo-3760067.jpeg?auto=compress&cs=tinysrgb&w=800" 
                alt="Community Investment" 
                className="w-full h-96 object-cover rounded-2xl shadow-lg"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl"></div>
            </div>
          </div>
        </div>
      </section>

      {/* Team Section */}
      <section className="bg-white py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center space-y-4 mb-16">
            <h2 className="text-4xl font-bold text-gray-900">Our Leadership Team</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Meet the experienced professionals dedicated to empowering Black wealth 
              and building a stronger community.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {team.map((member, index) => (
              <div key={index} className="text-center space-y-4">
                <div className="relative">
                  <img 
                    src={member.image} 
                    alt={member.name}
                    className="w-48 h-48 object-cover rounded-full mx-auto shadow-lg"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-full"></div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold text-gray-900">{member.name}</h3>
                  <p className="text-yellow-600 font-medium">{member.role}</p>
                  <p className="text-gray-600 text-sm mt-2 leading-relaxed">{member.bio}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Vision Section */}
      <section className="bg-gray-900 text-white py-16">
        <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 space-y-8">
          <div className="flex justify-center">
            <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center">
              <TrendingUp className="h-8 w-8 text-white" />
            </div>
          </div>
          <h2 className="text-4xl font-bold">Our Vision for 2040</h2>
          <p className="text-xl text-gray-300 leading-relaxed">
            By 2040, we envision a world where the Black-White wealth gap has been eliminated 
            through strategic community investments, financial education, and economic empowerment. 
            BlackCnote will be at the forefront of this transformation, having helped create 
            generational wealth for thousands of Black families.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button className="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-8 py-4 rounded-lg font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200">
              Join Our Mission
            </button>
            <button className="border-2 border-yellow-500 text-yellow-500 px-8 py-4 rounded-lg font-semibold hover:bg-yellow-500 hover:text-black transition-all duration-200">
              Learn More
            </button>
          </div>
        </div>
      </section>
    </div>
  );
};

export default About;